<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'nome'        => 'required|string|max:255',
        ]);

        $contrato = Contrato::where('id', $validated['contrato_id'])
            ->whereHas('prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        Lote::create($validated);

        return redirect()
            ->back() 
            ->with('success', 'Lote criado com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $lote = Lote::where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        $lote->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Nome do lote atualizado.');
    }
    
    public function destroy($id)
    {
         $lote = Lote::where('id', $id)
            ->whereHas('contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();
            
         $lote->delete(); 
         
         return redirect()
            ->back()
            ->with('success', 'Lote removido.');
    }
}