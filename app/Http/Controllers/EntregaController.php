<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Contrato;
// use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EntregaController extends Controller
{
    public function create(Request $request)
    {
        $contratoId = $request->get('contrato_id');

        $contrato = null;
        if ($contratoId) {
            $contrato = Contrato::with('lotes.itens')
                ->where('id', $contratoId)
                ->whereHas('prefeitura', function($q) {
                    $q->where('empresa_id', Auth::user()->empresa_id);
                })
                ->firstOrFail();
        }
        
        return view('entregas.create', compact('contrato'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contrato_id'  => 'required|exists:contratos,id',
            'data_entrega' => 'required|date',
            'comprovante'  => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'itens'        => 'array',
            'itens.*'      => 'nullable|numeric|min:0',
        ]);

        $contrato = Contrato::where('id', $request->contrato_id)
            ->whereHas('prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $path = null;
            if ($request->hasFile('comprovante')) {
                $path = $request->file('comprovante')->store('comprovantes/' . date('Y/m'), 'public');
            }

            $entrega = Entrega::create([
                'contrato_id'      => $contrato->id,
                'user_id'          => Auth::id(),
                'data_entrega'     => $request->data_entrega,
                'comprovante_path' => $path,
                'observacoes'      => $request->observacoes ?? null,
            ]);

            if ($request->has('itens')) {
                $dadosPivot = [];
                
                foreach ($request->itens as $itemId => $qtd) {
                    if ($qtd > 0) {                        
                        $dadosPivot[$itemId] = ['quantidade_entregue' => $qtd];
                    }
                }

                if (!empty($dadosPivot)) {
                    $entrega->itens()->attach($dadosPivot);
                }
            }

            DB::commit();

            return redirect()
                ->route('contratos.edit', $contrato->id)
                ->with('success', 'Entrega registrada com sucesso! Nº do Registro: ' . $entrega->id);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($path) Storage::disk('public')->delete($path);
            
            return back()
                ->withInput()
                ->with('error', 'Erro ao registrar entrega: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $entrega = Entrega::with(['itens', 'contrato.prefeitura', 'contrato.empresa'])
            ->where('id', $id)
            ->firstOrFail();
            
        return view('entregas.show', compact('entrega'));
    }
}