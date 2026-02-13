@extends('layouts.app')

@section('page-title', 'Painel do Contrato')
@section('page-subtitle', $contrato->numero_contrato . ' - ' . Str::limit($contrato->objeto, 50))

@section('content')

{{-- ALERTAS --}}
@if (session('success'))
    <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 fade-in">
        <p class="text-sm font-medium text-green-800"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="p-4 mb-6 rounded-lg bg-red-50 border border-red-200 fade-in">
        <p class="text-sm font-medium text-red-800"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="p-4 mb-6 rounded-lg bg-red-50 border border-red-200 fade-in">
        <ul class="list-disc list-inside text-sm text-red-800">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-8">

    {{-- DETALHES DO CONTRATO --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden" x-data="{ editing: false }">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">
                <i class="fas fa-file-contract mr-2 text-gray-400"></i> Dados do Contrato
            </h3>
            <button @click="editing = !editing" class="text-sm text-[#115e59] hover:underline">
                <span x-show="!editing"><i class="fas fa-edit"></i> Editar</span>
                <span x-show="editing"><i class="fas fa-times"></i> Cancelar</span>
            </button>
        </div>
        
        <div class="p-6">
            <form action="{{ route('contratos.update', $contrato->id) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" name="numero_contrato" value="{{ old('numero_contrato', $contrato->numero_contrato) }}" 
                            :disabled="!editing"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] disabled:bg-gray-100">
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Objeto</label>
                        <input type="text" name="objeto" value="{{ old('objeto', $contrato->objeto) }}" 
                            :disabled="!editing"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] disabled:bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Início Vigência</label>
                        <input type="date" name="data_inicio" value="{{ old('data_inicio', $contrato->data_inicio) }}" 
                            :disabled="!editing"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] disabled:bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fim Vigência</label>
                        <input type="date" name="data_fim" value="{{ old('data_fim', $contrato->data_fim) }}" 
                            :disabled="!editing"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] disabled:bg-gray-100">
                    </div>
                </div>

                <div x-show="editing" class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-[#115e59] text-white rounded-md hover:bg-[#0d4a46] text-sm">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GERENCIAMENTO DE LOTES E ITENS --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Lotes do Contrato</h3>
            
            {{-- Botão Novo Lote --}}
            <button onclick="document.getElementById('modalNovoLote').showModal()"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#062F43] rounded-lg hover:bg-[#084d6e] shadow-md transition-all">
                <i class="fas fa-layer-group"></i> Adicionar Lote
            </button>
        </div>

        @forelse($contrato->lotes as $lote)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden mb-6" x-data="{ showImport: false }">
                
                {{-- Cabeçalho do Lote --}}
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-md border border-gray-200 shadow-sm">
                            <i class="fas fa-box text-[#115e59]"></i>
                        </div>
                        <h4 class="font-bold text-gray-700 text-lg">{{ $lote->nome }}</h4>
                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">{{ $lote->itens->count() }} itens</span>
                    </div>

                    <div class="flex gap-2">
                        {{-- Botão Importar Excel --}}
                        <button @click="showImport = !showImport" 
                            class="text-sm px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-file-excel"></i> Importar Itens
                        </button>

                        {{-- Botão Novo Item Manual --}}
                        <button onclick="openModalItem({{ $lote->id }}, '{{ $lote->nome }}')"
                            class="text-sm px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors flex items-center gap-2">
                            <i class="fas fa-plus"></i> Novo Item
                        </button>
                        
                        {{-- Excluir Lote --}}
                        @if($lote->itens->count() == 0)
                        <form action="{{ route('lotes.destroy', $lote->id) }}" method="POST" onsubmit="return confirm('Excluir este lote?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm px-3 py-1.5 text-red-500 hover:bg-red-50 rounded transition-colors" title="Excluir Lote Vazio">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Área de Importação --}}
                <div x-show="showImport" class="bg-green-50 p-4 border-b border-green-100" style="display: none;">
                    <form action="{{ route('itens.import') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-4">
                        @csrf
                        <input type="hidden" name="lote_id" value="{{ $lote->id }}">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-green-800 mb-1">Selecione a Planilha (.xlsx, .csv)</label>
                            <input type="file" name="arquivo_excel" required accept=".xlsx,.xls,.csv" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-green-700 text-white text-sm font-bold rounded hover:bg-green-800">
                            Processar Importação
                        </button>
                        <a href="#" class="text-xs text-green-700 underline self-center ml-2">Baixar Modelo</a>
                    </form>
                </div>

                {{-- Tabela de Itens --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Und</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">V. Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($lote->itens as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">{{ $item->numero_item ?? $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900 font-medium">{{ Str::limit($item->descricao, 60) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">{{ $item->unidade }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 font-bold text-right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('itens.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Remover este item?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm bg-gray-50">
                                        Nenhum item cadastrado neste lote. 
                                        <br>Use o botão "Importar" ou "Novo Item".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border-2 border-dashed border-gray-300">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">Nenhum lote criado para este contrato.</p>
                <p class="text-sm text-gray-400 mb-4">Crie um lote para começar a adicionar itens.</p>
                <button onclick="document.getElementById('modalNovoLote').showModal()" class="text-[#115e59] hover:underline font-bold">
                    Criar Primeiro Lote
                </button>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL NOVO LOTE --}}
<dialog id="modalNovoLote" class="rounded-lg shadow-xl p-0 w-full max-w-md backdrop:bg-gray-900/50">
    <div class="bg-white">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Novo Lote</h3>
            <button onclick="document.getElementById('modalNovoLote').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('lotes.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="contrato_id" value="{{ $contrato->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Lote</label>
                <input type="text" name="nome" required placeholder="Ex: Lote 01 - Material de Limpeza" 
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modalNovoLote').close()" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-[#115e59] hover:bg-[#0d4a46] rounded-lg shadow">Criar Lote</button>
            </div>
        </form>
    </div>
</dialog>

{{-- MODAL NOVO ITEM --}}
<dialog id="modalNovoItem" class="rounded-lg shadow-xl p-0 w-full max-w-2xl backdrop:bg-gray-900/50">
    <div class="bg-white">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Adicionar Item ao <span id="spanNomeLote" class="text-[#115e59]"></span></h3>
            <button onclick="document.getElementById('modalNovoItem').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('itens.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="lote_id" id="inputLoteId">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- N Item --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Nº Item</label>
                    <input type="number" name="numero_item" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>
                
                {{-- Descrição --}}
                <div class="md:col-span-10">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Descrição *</label>
                    <input type="text" name="descricao" required class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                {{-- Unidade --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase">UND *</label>
                    <input type="text" name="unidade" required placeholder="UN" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                {{-- Quantidade --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Quantidade *</label>
                    <input type="number" step="0.0001" name="quantidade" id="qtd" required oninput="calcularTotal()" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                {{-- Valor Unit --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Valor Unit. *</label>
                    <input type="number" step="0.01" name="valor_unitario" id="vlr" required oninput="calcularTotal()" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                {{-- Total --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Total Estimado</label>
                    <input type="text" id="total" readonly class="w-full bg-gray-100 border-gray-300 rounded text-gray-500 font-bold">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                <button type="button" onclick="document.getElementById('modalNovoItem').close()" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-[#115e59] hover:bg-[#0d4a46] rounded-lg shadow">Salvar Item</button>
            </div>
        </form>
    </div>
</dialog>

@endsection

@push('scripts')
<script>
    function openModalItem(loteId, loteNome) {
        document.getElementById('inputLoteId').value = loteId;
        document.getElementById('spanNomeLote').innerText = loteNome;
        
        document.getElementById('qtd').value = '';
        document.getElementById('vlr').value = '';
        document.getElementById('total').value = '';
        
        document.getElementById('modalNovoItem').showModal();
    }

    function calcularTotal() {
        const qtd = parseFloat(document.getElementById('qtd').value) || 0;
        const vlr = parseFloat(document.getElementById('vlr').value) || 0;
        const total = qtd * vlr;
        
        document.getElementById('total').value = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
</script>
@endpush