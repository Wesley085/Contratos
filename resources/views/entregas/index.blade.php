@extends('layouts.app')

@section('page-title', 'Histórico de Entregas')
@section('page-subtitle', 'Consulte e gerencie os recibos emitidos.')

@section('content')

    {{-- ALERTA DE SUCESSO --}}
    @if (session('success'))
        <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 slide-in">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-hidden bg-white shadow-sm rounded-xl fade-in">
        
        {{-- CABEÇALHO --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-700">Recibos Emitidos</h3>
                <p class="text-sm text-gray-500">Lista ordenada pelas entregas mais recentes.</p>
            </div>
            
            {{-- 
               NOTA: Mantivemos o link para Prefeituras porque para criar uma entrega
               é OBRIGATÓRIO selecionar um contrato primeiro.
            --}}
            <a href="{{ route('entregas.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-[#062F43] rounded-lg hover:bg-[#084d6e] hover:shadow-lg hover:-translate-y-0.5">
                <i class="fas fa-plus"></i> Nova Entrega
            </a>
        </div>

        {{-- TABELA --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Data / Recibo</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Contrato</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Responsável</th>
                        <th class="px-6 py-3 text-xs font-bold text-center text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($entregas as $entrega)
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            {{-- DATA E NÚMERO --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($entrega->data_entrega)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 font-mono">
                                            #{{ str_pad($entrega->id, 5, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- CLIENTE --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $entrega->contrato->prefeitura->nome }}</div>
                                <div class="text-xs text-gray-500" data-type="cnpj">{{ $entrega->contrato->prefeitura->cnpj }}</div>
                            </td>

                            {{-- CONTRATO --}}
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $entrega->contrato->numero_contrato }}
                                </span>
                            </td>

                            {{-- RESPONSÁVEL --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $entrega->user->name ?? 'Sistema' }}
                            </td>

                            {{-- AÇÕES (ICONES) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-3">

                                    {{-- Editar (Se a rota existir) --}}
                                    <a href="{{ route('entregas.edit', $entrega->id) }}"
                                        class="text-amber-500 hover:text-amber-700 transition-colors"
                                        title="Editar Entrega">
                                         <i class="fas fa-edit text-lg"></i>
                                    </a>

                                    <a href="{{ route('entregas.recibo', $entrega->id) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 transition-colors"
                                        title="Imprimir Recibo">
                                            <i class="fas fa-file-pdf text-lg"></i>
                                    </a>

                                    {{-- Excluir --}}
                                    <form action="{{ route('entregas.destroy', $entrega->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja cancelar este recibo? O estoque será devolvido automaticamente.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Excluir">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-truck-loading text-gray-300 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Nenhuma entrega registrada</h3>
                                    <p class="text-sm text-gray-500 mt-1 mb-4">Para fazer uma entrega, selecione um contrato em "Prefeituras".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINAÇÃO --}}
        @if($entregas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $entregas->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function formatCNPJ(value) {
                return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
            }
            document.querySelectorAll("div[data-type='cnpj']").forEach(function(el) {
                let text = el.innerText.trim();
                let onlyNumbers = text.replace(/\D/g, '');
                if (onlyNumbers.length === 14) {
                    el.innerText = formatCNPJ(onlyNumbers);
                }
            });
        });
    </script>
    @endpush
@endsection