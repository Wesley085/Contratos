<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItensImport;

class ItemController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lote_id'        => 'required|exists:lotes,id',
            'numero_item'    => 'nullable|integer',
            'descricao'      => 'required|string',
            'unidade'        => 'required|string|max:10',
            'quantidade'     => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $lote = Lote::where('id', $validated['lote_id'])
            ->whereHas('contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        $validated['valor_total'] = $validated['quantidade'] * $validated['valor_unitario'];

        Item::create($validated);

        return redirect()
            ->back() 
            ->with('success', 'Item adicionado com sucesso.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'arquivo_excel' => 'required|mimes:xlsx,xls,csv',
        ]);

        $lote = Lote::where('id', $request->lote_id)
            ->whereHas('contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        try {
            DB::beginTransaction();
            Excel::import(new ItensImport($lote->id), $request->file('arquivo_excel'));
            DB::commit();

            return redirect()->back()->with('success', 'Itens importados com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro na importação: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        $item = Item::where('id', $id)->firstOrFail();
        $item->delete();
        return back()->with('success', 'Item removido.');
    }
}