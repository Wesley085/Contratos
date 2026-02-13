<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Prefeitura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefeitura_id'   => 'required|exists:prefeituras,id',
            'numero_contrato' => 'required|string|max:255',
            'objeto'          => 'required|string',
            'data_inicio'     => 'nullable|date',
            'data_fim'        => 'nullable|date',
        ]);

        $prefeitura = Prefeitura::where('id', $validated['prefeitura_id'])
            ->where('empresa_id', Auth::user()->empresa_id)
            ->firstOrFail();

        $contrato = Contrato::create($validated);

        return redirect()
            ->route('contratos.edit', $contrato->id)
            ->with('success', 'Contrato criado! Agora adicione os Lotes e Itens.');
    }

    public function edit($id)
    {
        $empresaId = Auth::user()->empresa_id;

        $contrato = Contrato::where('id', $id)
            ->whereHas('prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->with(['prefeitura', 'lotes.itens']) 
            ->firstOrFail();

        return view('contratos.edit', compact('contrato'));
    }

    public function update(Request $request, $id)
    {
        $empresaId = Auth::user()->empresa_id;

        $contrato = Contrato::where('id', $id)
            ->whereHas('prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'numero_contrato' => 'required|string|max:255',
            'objeto'          => 'required|string',
            'data_inicio'     => 'nullable|date',
            'data_fim'        => 'nullable|date',
            'ativo'           => 'boolean'
        ]);

        $contrato->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Dados do contrato atualizados.');
    }

    public function destroy($id)
    {
        $empresaId = Auth::user()->empresa_id;

        $contrato = Contrato::where('id', $id)
            ->whereHas('prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->firstOrFail();

        $contrato->delete();

        return redirect()
            ->route('prefeituras.edit', $contrato->prefeitura_id)
            ->with('success', 'Contrato arquivado com sucesso.');
    }
}