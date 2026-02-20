<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Contrato;
use App\Models\Prefeitura;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EntregaController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->empresa_id;

        $entregas = Entrega::with(['contrato.prefeitura', 'user'])
            ->whereHas('contrato.prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            // ->orderBy('data_entrega', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('entregas.index', compact('entregas'));
    }

    public function create(Request $request)
    {
        $empresaId = Auth::user()->empresa_id;

        $prefeituras = Prefeitura::where('empresa_id', $empresaId)
            ->with(['contratos' => function($q) {
                $q->select('id', 'prefeitura_id', 'numero_contrato', 'objeto')->orderBy('created_at', 'desc');
            }])
            ->orderBy('nome')
            ->get();

        $contratoSelecionado = null;
        if ($request->has('contrato_id')) {
            $contratoSelecionado = Contrato::where('id', $request->get('contrato_id'))
                ->whereHas('prefeitura', fn($q) => $q->where('empresa_id', $empresaId))
                ->with(['prefeitura', 'lotes.itens' => function($query) {
                    $query->withSum('entregas as ja_entregue', 'entrega_item.quantidade_entregue');
                }])
                ->first();
        }

        return view('entregas.create', compact('prefeituras', 'contratoSelecionado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contrato_id'  => 'required|exists:contratos,id',
            'data_entrega' => 'required|date',
            'comprovante'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'itens'        => 'array',
        ]);

        $contrato = Contrato::where('id', $request->contrato_id)
            ->whereHas('prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        $itensEnviados = array_filter($request->itens ?? [], fn($qtd) => $qtd > 0);

        if (empty($itensEnviados)) {
            return back()->withInput()->with('error', 'Você precisa informar a quantidade de pelo menos um item.');
        }

        $itensDb = Item::whereIn('id', array_keys($itensEnviados))
                        ->withSum('entregas as ja_entregue', 'entrega_item.quantidade_entregue')
                        ->get();

        foreach ($itensDb as $item) {
            $qtdSolicitada = $itensEnviados[$item->id];
            $qtdJaEntregue = $item->ja_entregue ?? 0;
            $saldoDisponivel = $item->quantidade - $qtdJaEntregue;

            if (round($qtdSolicitada, 4) > round($saldoDisponivel, 4)) {
                return back()
                    ->withInput()
                    ->with('error', "Erro no item '{$item->descricao}': O saldo disponível é de {$saldoDisponivel} {$item->unidade}, mas você tentou entregar {$qtdSolicitada}.");
            }
        }

        try {
            DB::beginTransaction();

            $path = null;
            if ($request->hasFile('comprovante')) {
                $path = $request->file('comprovante')->store('comprovantes/' . date('Y'), 'public');
            }

            $entrega = Entrega::create([
                'contrato_id'      => $contrato->id,
                'user_id'          => Auth::id(),
                'data_entrega'     => $request->data_entrega,
                'comprovante_path' => $path,
                'observacoes'      => $request->observacoes,
            ]);

            $attachData = [];
            foreach ($itensEnviados as $id => $qtd) {
                $attachData[$id] = ['quantidade_entregue' => $qtd];
            }

            $entrega->itens()->attach($attachData);

            DB::commit();

            return redirect()
                ->route('entregas.index')
                ->with('success', 'Entrega registrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($path) Storage::disk('public')->delete($path);
            
            return back()
                ->withInput()
                ->with('error', 'Erro ao processar entrega: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $entrega = Entrega::with(['contrato.prefeitura', 'itens.lote', 'user'])
            ->where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        return view('entregas.show', compact('entrega'));
    }

    public function edit($id)
    {
        $empresaId = Auth::user()->empresa_id;

        $entrega = Entrega::where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->with(['contrato.lotes.itens' => function($query) {
                $query->withSum('entregas as ja_entregue', 'entrega_item.quantidade_entregue');
            }])
            ->firstOrFail();

        return view('entregas.edit', compact('entrega'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'data_entrega' => 'required|date',
            'comprovante'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'itens'        => 'array',
        ]);

        $entrega = Entrega::with('itens')->where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        $itensEnviados = array_filter($request->itens ?? [], fn($qtd) => $qtd > 0);

        if (empty($itensEnviados)) {
            return back()->withInput()->with('error', 'A entrega não pode ficar sem itens. Se deseja cancelar, exclua a entrega.');
        }

        $itensDb = Item::whereIn('id', array_keys($itensEnviados))
                        ->withSum('entregas as total_entregue', 'entrega_item.quantidade_entregue')
                        ->get();

        foreach ($itensDb as $item) {
            $novaQtdSolicitada = $itensEnviados[$item->id];
            $totalGastoGlobal = $item->total_entregue ?? 0;
            $qtdNestaEntrega = $entrega->itens->find($item->id)->pivot->quantidade_entregue ?? 0;
            $saldoParaEdicao = ($item->quantidade - $totalGastoGlobal) + $qtdNestaEntrega;

            if (round($novaQtdSolicitada, 4) > round($saldoParaEdicao, 4)) {
                return back()
                    ->withInput()
                    ->with('error', "Erro no item '{$item->descricao}': O limite para edição é {$saldoParaEdicao} (Saldo Atual + Qtd desta entrega). Você tentou usar {$novaQtdSolicitada}.");
            }
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('comprovante')) {
                if ($entrega->comprovante_path && Storage::disk('public')->exists($entrega->comprovante_path)) {
                    Storage::disk('public')->delete($entrega->comprovante_path);
                }
                $entrega->comprovante_path = $request->file('comprovante')->store('comprovantes/' . date('Y'), 'public');
            }

            $entrega->update([
                'data_entrega' => $request->data_entrega,
                'observacoes'  => $request->observacoes,
            ]);

            $syncData = [];
            foreach ($itensEnviados as $itemId => $qtd) {
                $syncData[$itemId] = ['quantidade_entregue' => $qtd];
            }

            $entrega->itens()->sync($syncData);

            DB::commit();

            return redirect()
                ->route('entregas.show', $entrega->id)
                ->with('success', 'Entrega atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function gerarRecibo($id)
    {
        $empresaId = Auth::user()->empresa_id;

        $entrega = Entrega::with(['contrato.prefeitura.empresa', 'itens.lote', 'user'])
            ->where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('entregas.pdf', compact('entrega'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("recibo_entrega_{$entrega->id}.pdf");
    }

    public function destroy($id)
    {
        $empresaId = Auth::user()->empresa_id;

        $entrega = Entrega::where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->firstOrFail();

        try {
            DB::beginTransaction();

            if ($entrega->comprovante_path && Storage::disk('public')->exists($entrega->comprovante_path)) {
                Storage::disk('public')->delete($entrega->comprovante_path);
            }

            $entrega->itens()->detach();

            $entrega->delete();

            DB::commit();

            return redirect()
                ->route('entregas.index')
                ->with('success', 'Entrega cancelada e saldo dos itens estornado ao contrato.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('entregas.index')
                ->with('error', 'Erro ao cancelar entrega: ' . $e->getMessage());
        }
    }
}