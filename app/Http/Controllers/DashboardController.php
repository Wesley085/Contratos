<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Contrato;
use App\Models\Prefeitura;
use App\Models\Entrega;
use App\Models\Item;

class DashboardController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->empresa_id;
        $totalPrefeituras = Prefeitura::where('empresa_id', $empresaId)->count();
    
        $totalContratos = Contrato::whereHas('prefeitura', function($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })->count();

        $contratosAtivos = Contrato::whereHas('prefeitura', function($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })
        ->where('ativo', true) 
        ->where(function($q) {
            $q->whereNull('data_fim')->orWhere('data_fim', '>=', now());
        })
        ->count();

        $valorTotalContratado = Item::whereHas('lote.contrato.prefeitura', function($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })->sum('valor_total');

        $valorTotalEntregue = DB::table('entrega_item')
            ->join('itens', 'entrega_item.item_id', '=', 'itens.id')
            ->join('lotes', 'itens.lote_id', '=', 'lotes.id')
            ->join('contratos', 'lotes.contrato_id', '=', 'contratos.id')
            ->join('prefeituras', 'contratos.prefeitura_id', '=', 'prefeituras.id')
            ->where('prefeituras.empresa_id', $empresaId)
            ->sum(DB::raw('entrega_item.quantidade_entregue * itens.valor_unitario'));

        $porcentagemExecucao = $valorTotalContratado > 0 
            ? ($valorTotalEntregue / $valorTotalContratado) * 100 
            : 0;

        $ultimasEntregas = Entrega::with(['contrato.prefeitura', 'user'])
            ->whereHas('contrato.prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $contratosVencendo = Contrato::with('prefeitura')
            ->whereHas('prefeitura', function($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->where('data_fim', '>=', now())
            ->where('data_fim', '<=', now()->addDays(30))
            ->orderBy('data_fim', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalPrefeituras',
            'totalContratos',
            'contratosAtivos',
            'valorTotalContratado',
            'valorTotalEntregue',
            'porcentagemExecucao',
            'ultimasEntregas',
            'contratosVencendo'
        ));
    }
}