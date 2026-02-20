@extends('layouts.app')

@section('page-title', 'Detalhes da Entrega')
@section('page-subtitle', 'Visualizando recibo #' . str_pad($entrega->id, 5, '0', STR_PAD_LEFT))

@section('content')

    {{-- FEEDBACK --}}
    @if (session('success'))
        <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 fade-in">
            <p class="text-sm font-medium text-green-800"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">
        
        {{-- COLUNA PRINCIPAL (ITENS) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- CABEÇALHO DA ENTREGA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-box-open text-[#184ea4]"></i> Itens Entregues
                    </h3>
                    <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                        {{ $entrega->itens->count() }} itens
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 w-12 text-center">#</th>
                                <th class="px-6 py-3">Descrição / Lote</th>
                                <th class="px-6 py-3 text-center">Qtd.</th>
                                <th class="px-6 py-3 text-right">V. Unit.</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php $totalGeral = 0; @endphp
                            @foreach($entrega->itens as $index => $item)
                                @php 
                                    $subtotal = $item->pivot->quantidade_entregue * $item->valor_unitario; 
                                    $totalGeral += $subtotal;
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-400 text-xs">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800">{{ $item->descricao }}</div>
                                        @if($item->lote)
                                            <div class="text-xs text-[#184ea4] mt-0.5 flex items-center gap-1">
                                                <i class="fas fa-layer-group text-[10px]"></i> {{ $item->lote->nome }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono text-gray-600 bg-gray-50/50">
                                        {{ number_format($item->pivot->quantidade_entregue, 2, ',', '.') }} {{ $item->unidade }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-500">
                                        R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800 font-mono">
                                        R$ {{ number_format($subtotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-600 uppercase text-xs tracking-wider">Valor Total da Entrega</td>
                                <td class="px-6 py-4 text-right font-bold text-[#184ea4] text-lg font-mono">
                                    R$ {{ number_format($totalGeral, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- OBSERVAÇÕES --}}
            @if($entrega->observacoes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Observações</h4>
                <p class="text-gray-600 text-sm bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                    {{ $entrega->observacoes }}
                </p>
            </div>
            @endif

        </div>

        {{-- COLUNA LATERAL (INFO) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- CARD DE AÇÕES --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-700 mb-4">Ações</h3>
                
                <div class="space-y-3">
                    {{-- Botão Imprimir (Destaque Laranja) --}}
                    <a href="{{ route('entregas.recibo', $entrega->id) }}" target="_blank"
                       class="flex items-center justify-center gap-2 w-full py-3 bg-[#f1974e] hover:bg-[#d9823b] text-white rounded-lg font-bold shadow-sm transition-all hover:-translate-y-0.5">
                        <i class="fas fa-print"></i> Imprimir Recibo
                    </a>

                    {{-- Botão Editar --}}
                    <a href="{{ route('entregas.edit', $entrega->id) }}"
                       class="flex items-center justify-center gap-2 w-full py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit text-blue-500"></i> Editar Dados
                    </a>

                    <div class="border-t border-gray-100 my-2"></div>

                    {{-- Botão Excluir --}}
                    <form action="{{ route('entregas.destroy', $entrega->id) }}" method="POST" onsubmit="return confirm('Tem certeza? O saldo será estornado ao contrato.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center justify-center gap-2 w-full py-2 text-red-500 hover:bg-red-50 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-trash-alt"></i> Cancelar Recibo
                        </button>
                    </form>
                </div>
            </div>

            {{-- CARD INFO CONTRATO --}}
            <div class="bg-[#234c8c] rounded-xl shadow-sm p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-2 -mr-2 opacity-10">
                    <i class="fas fa-file-contract text-9xl"></i>
                </div>
                
                <p class="text-xs font-bold text-blue-200 uppercase tracking-wider mb-1">Cliente</p>
                <h4 class="text-lg font-bold mb-4">{{ $entrega->contrato->prefeitura->nome }}</h4>
                
                <div class="space-y-3 relative z-10">
                    <div>
                        <p class="text-xs text-blue-200">Contrato</p>
                        <p class="font-mono font-bold">{{ $entrega->contrato->numero_contrato }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-200">Data Entrega</p>
                        <p class="font-bold">{{ $entrega->data_entrega->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-200">Responsável</p>
                        <p class="text-sm">{{ $entrega->user->name ?? 'Sistema' }}</p>
                    </div>
                </div>
            </div>

            {{-- CARD COMPROVANTE --}}
            @if($entrega->comprovante_path)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-paperclip text-gray-400"></i> Anexos
                </h3>
                
                <a href="{{ asset('storage/' . $entrega->comprovante_path) }}" target="_blank" 
                   class="group flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-[#184ea4] hover:bg-blue-50 transition-all">
                    <div class="h-10 w-10 bg-red-100 text-red-500 rounded flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="far fa-file-pdf text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">Comprovante.pdf</p>
                        <p class="text-xs text-gray-500">Clique para visualizar</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                </a>
            </div>
            @else
            <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                <i class="fas fa-file-upload text-gray-300 text-3xl mb-2"></i>
                <p class="text-sm text-gray-500">Nenhum comprovante anexado.</p>
            </div>
            @endif

        </div>
    </div>

@endsection