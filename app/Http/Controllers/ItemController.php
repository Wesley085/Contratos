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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'descricao'      => 'required|string',
            'unidade'        => 'required|string|max:10',
            'quantidade'     => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $item = Item::where('id', $id)
            ->whereHas('lote.contrato.prefeitura', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->firstOrFail();

        $validated['valor_total'] = $validated['quantidade'] * $validated['valor_unitario'];

        $item->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Item atualizado com sucesso.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'arquivo_excel' => 'required|file|mimes:xlsx,xls,csv,txt',
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
    
    public function downloadModelo()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=modelo_importacao_itens.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['descricao', 'unidade', 'quantidade', 'valor_unitario'], ';');
            fputcsv($file, ['Cimento CP II - Saco 50kg', 'SC', '100', '35.50'], ';');
            fputcsv($file, ['Tijolo Cerâmico 8 furos', 'MIL', '5.5', '850.00'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $item = Item::where('id', $id)
            ->whereHas('lote.contrato.prefeitura', fn($q) => $q->where('empresa_id', Auth::user()->empresa_id))
            ->firstOrFail();

        $item->delete();
        return back()->with('success', 'Item removido.');
    }
}