@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral dos contratos e entregas.')

@section('content')

{{-- 1. CARDS DE DESTAQUE (Financeiro e Volume) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    {{-- Card 1: Valor Contratado --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#184ea4] hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Total em Contratos</p>
                <h3 class="text-2xl font-bold text-[#234c8c]">R$ {{ number_format($valorTotalContratado, 2, ',', '.') }}</h3>
            </div>
            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center text-[#184ea4]">
                <i class="fas fa-file-invoice-dollar text-xl"></i>
            </div>
        </div>
        <div class="text-xs text-gray-400">Soma de todos os itens cadastrados</div>
    </div>

    {{-- Card 2: Valor Executado (Entregue) --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#f1974e] hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Total Executado</p>
                <h3 class="text-2xl font-bold text-[#f1974e]">R$ {{ number_format($valorTotalEntregue, 2, ',', '.') }}</h3>
            </div>
            <div class="h-12 w-12 rounded-full bg-orange-50 flex items-center justify-center text-[#f1974e]">
                <i class="fas fa-truck-loading text-xl"></i>
            </div>
        </div>
        
        {{-- Barra de Progresso --}}
        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
            <div class="bg-[#f1974e] h-1.5 rounded-full" style="width: {{ min($porcentagemExecucao, 100) }}%"></div>
        </div>
        <div class="text-xs text-gray-500 mt-1 font-bold">{{ number_format($porcentagemExecucao, 1) }}% realizado</div>
    </div>

    {{-- Card 3: Contratos Ativos --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#84a4cd] hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Contratos Ativos</p>
                <h3 class="text-2xl font-bold text-gray-700">{{ $contratosAtivos }} <span class="text-sm text-gray-400 font-normal">/ {{ $totalContratos }}</span></h3>
            </div>
            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center text-[#84a4cd]">
                <i class="fas fa-file-contract text-xl"></i>
            </div>
        </div>
        <div class="text-xs text-gray-400">Contratos vigentes no sistema</div>
    </div>

    {{-- Card 4: Prefeituras --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#234c8c] hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Clientes</p>
                <h3 class="text-2xl font-bold text-[#234c8c]">{{ $totalPrefeituras }}</h3>
            </div>
            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center text-[#234c8c]">
                <i class="fas fa-landmark text-xl"></i>
            </div>
        </div>
        <div class="text-xs text-gray-400">Prefeituras cadastradas</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- 2. ÚLTIMAS ENTREGAS (Ocupa 2 colunas) --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h3 class="font-bold text-gray-700"><i class="fas fa-history mr-2 text-[#84a4cd]"></i> Últimas Entregas</h3>
            <a href="{{ route('entregas.index') }}" class="text-xs font-bold text-[#184ea4] hover:underline">Ver todas</a>
        </div>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3">Recibo</th>
                        <th class="px-6 py-3">Prefeitura</th>
                        <th class="px-6 py-3">Data</th>
                        <th class="px-6 py-3 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ultimasEntregas as $entrega)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-3 font-mono text-[#234c8c] font-bold">
                            #{{ str_pad($entrega->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ Str::limit($entrega->contrato->prefeitura->nome, 25) }}
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $entrega->data_entrega->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('entregas.show', $entrega->id) }}" class="text-[#f1974e] hover:text-[#d9823b]" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                            Nenhuma entrega registrada recentemente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. ALERTA DE VENCIMENTOS (Ocupa 1 coluna) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-orange-50 rounded-t-xl">
            <h3 class="font-bold text-orange-800"><i class="fas fa-exclamation-circle mr-2"></i> Vencendo em Breve</h3>
        </div>

        <div class="p-4 flex-1 overflow-y-auto">
            @forelse($contratosVencendo as $contrato)
                <div class="flex items-start gap-3 mb-4 p-3 rounded-lg border border-orange-100 bg-orange-50/30 hover:bg-orange-50 transition-colors">
                    <div class="text-orange-400 mt-1">
                        <i class="far fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-700">{{ $contrato->numero_contrato }}</h4>
                        <p class="text-xs text-gray-500">{{ $contrato->prefeitura->nome }}</p>
                        <p class="text-xs font-bold text-orange-600 mt-1">
                            Vence: {{ \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y') }}
                            <span class="font-normal text-gray-400">({{ \Carbon\Carbon::parse($contrato->data_fim)->diffForHumans() }})</span>
                        </p>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 py-8">
                    <i class="fas fa-check-circle text-4xl mb-3 text-green-100"></i>
                    <p>Tudo tranquilo!</p>
                    <p class="text-xs">Nenhum contrato vencendo nos próximos 30 dias.</p>
                </div>
            @endforelse
        </div>
        
        {{-- <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl text-center">
            <a href="{{ route('contratos.index') }}" class="text-xs font-bold text-[#184ea4] hover:underline">Ver todos os contratos</a>
        </div> --}}
    </div>

</div>

@endsection