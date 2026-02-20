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
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden mb-6 transition-all hover:shadow-md" x-data="{ showImport: false }">
                
                {{-- Cabeçalho do Lote --}}
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    
                    {{-- Título --}}
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-[#184ea4]">
                            <i class="fas fa-layer-group text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-gray-800 text-lg">{{ $lote->nome }}</h4>
                                <button onclick="openModalEditarLote({{ $lote->id }}, '{{ $lote->nome }}')" class="text-gray-400 hover:text-[#184ea4] transition-colors" title="Editar Nome">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            </div>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">
                                {{ $lote->itens->count() }} itens cadastrados
                            </span>
                        </div>
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="flex flex-wrap items-center gap-2">
                        
                        {{-- Botão Importar (Estilo Ghost Verde) --}}
                        <button
                            @click="showImport = !showImport"
                            :class="showImport 
                                ? 'bg-green-50 border-green-200 text-green-700' 
                                : 'bg-white border-green-600 text-green-600 hover:bg-green-600 hover:text-white'"
                            class="group px-4 py-2 text-sm font-semibold border rounded-lg transition-all flex items-center gap-2 shadow-sm">

                            <i class="fas fa-file-excel"
                            :class="showImport ? '' : 'text-green-600 group-hover:text-white'">
                            </i>

                            <span x-text="showImport ? 'Fechar Importação' : 'Importar Excel'"></span>
                        </button>


                        {{-- Botão Novo Item) --}}
                        <button onclick="openModalItem({{ $lote->id }}, '{{ $lote->nome }}')"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[#184ea4] border border-[#184ea4] rounded-lg hover:bg-[#133b7a] transition-all flex items-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <i class="fas fa-plus"></i> Novo Item
                        </button>
                        
                        {{-- Excluir Lote --}}
                        @if($lote->itens->count() == 0)
                        <form action="{{ route('lotes.destroy', $lote->id) }}" method="POST" onsubmit="return confirm('Excluir este lote?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="h-10 w-10 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Excluir Lote Vazio">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Área de Importação --}}
                <div x-show="showImport" x-collapse style="display: none;">
                    <div class="bg-gray-50 p-6 border-b border-gray-200 shadow-inner">
                        <div class="flex flex-col md:flex-row gap-8">
                            
                            {{-- Lado Esquerdo: Instruções e Modelo --}}
                            <div class="md:w-1/3 space-y-4">
                                <div>
                                    <h5 class="font-bold text-gray-700 mb-1">1. Baixe o Modelo</h5>
                                    <p class="text-xs text-gray-500 mb-3">Use nossa planilha padrão para evitar erros.</p>
                                    <a href="{{ route('itens.modelo') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:border-[#f1974e] hover:text-[#f1974e] transition-colors shadow-sm">
                                        <i class="fas fa-download text-[#f1974e]"></i> Baixar Planilha Modelo
                                    </a>
                                </div>
                                
                                <div class="text-xs text-gray-500 bg-blue-50 p-3 rounded border border-blue-100">
                                    <strong>Atenção:</strong> As colunas obrigatórias são: <br>
                                    <code class="text-blue-700">descricao</code>, <code class="text-blue-700">unidade</code>, <code class="text-blue-700">quantidade</code>, <code class="text-blue-700">valor_unitario</code>.
                                </div>
                            </div>

                            {{-- Lado Direito: Upload --}}
                            <div class="md:w-2/3">
                                <h5 class="font-bold text-gray-700 mb-2">2. Envie o Arquivo</h5>
                                <form action="{{ route('itens.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="lote_id" value="{{ $lote->id }}">
                                    
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1">
                                            <input type="file" name="arquivo_excel" required accept=".xlsx,.xls,.csv" 
                                                class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2.5 file:px-4
                                                file:rounded-lg file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-[#184ea4] file:text-white
                                                hover:file:bg-[#133b7a]
                                                cursor-pointer border border-gray-300 rounded-lg bg-white">
                                        </div>
                                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow hover:bg-green-700 transition-all">
                                            <i class="fas fa-cloud-upload-alt mr-2"></i> Enviar
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Tabela de Itens --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">Und</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Qtd</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">V. Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($lote->itens as $item)
                                <tr class="hover:bg-blue-50/50 group transition-colors">
                                    <td class="px-6 py-3 text-sm text-gray-400 text-center font-mono">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900 font-medium">{{ Str::limit($item->descricao, 60) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-gray-50/50 font-medium">{{ $item->unidade }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-[#184ea4] font-bold text-right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3 opacity-60 group-hover:opacity-100 transition-opacity">
                                            {{-- Botão Editar Item --}}
                                            <button onclick="openModalEditarItem({{ $item->id }}, '{{ addslashes($item->descricao) }}', '{{ $item->unidade }}', {{ $item->quantidade }}, {{ $item->valor_unitario }})" 
                                                class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar Item">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('itens.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Remover este item?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Excluir Item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-clipboard-list text-3xl mb-3 opacity-20"></i>
                                            <p class="font-medium">Este lote está vazio.</p>
                                            <p class="text-xs mt-1">Clique em "Novo Item" ou "Importar Excel" para começar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border-2 border-dashed border-gray-300"></div>
        @endforelse
    </div>
</div>

{{-- MODAL NOVO LOTE --}}
<dialog id="modalNovoLote" class="m-auto rounded-lg shadow-xl p-0 w-full max-w-md backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
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

{{-- MODAL EDITAR LOTE --}}
<dialog id="modalEditarLote" class="m-auto rounded-lg shadow-xl p-0 w-full max-w-md backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
    <div class="bg-white">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Editar Lote</h3>
            <button onclick="document.getElementById('modalEditarLote').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditarLote" method="POST" class="p-6">
            @csrf @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Lote</label>
                <input type="text" name="nome" id="edit_lote_nome" required 
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modalEditarLote').close()" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow">Atualizar</button>
            </div>
        </form>
    </div>
</dialog>

{{-- MODAL NOVO ITEM --}}
<dialog id="modalNovoItem" class="m-auto rounded-lg shadow-xl p-0 w-full max-w-2xl backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
    <div class="bg-white">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Adicionar Item ao <span id="spanNomeLote" class="text-[#115e59]"></span></h3>
            <button onclick="document.getElementById('modalNovoItem').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('itens.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="lote_id" id="inputLoteId">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">                
                <div class="md:col-span-12">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Descrição *</label>
                    <input type="text" name="descricao" required class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase">UND *</label>
                    <input type="text" name="unidade" required placeholder="UN" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Quantidade *</label>
                    <input type="number" step="0.0001" name="quantidade" id="qtd" required oninput="calcularTotal('qtd', 'vlr', 'total')" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Valor Unit. *</label>
                    <input type="number" step="0.01" name="valor_unitario" id="vlr" required oninput="calcularTotal('qtd', 'vlr', 'total')" class="w-full border-gray-300 rounded focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                <div class="md:col-span-12">
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

{{-- MODAL EDITAR ITEM --}}
<dialog id="modalEditarItem" class="m-auto rounded-lg shadow-xl p-0 w-full max-w-2xl backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
    <div class="bg-white">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Editar Item</h3>
            <button onclick="document.getElementById('modalEditarItem').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditarItem" method="POST" class="p-6">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">                
                <div class="md:col-span-12">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Descrição *</label>
                    <input type="text" name="descricao" id="edit_descricao" required class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase">UND *</label>
                    <input type="text" name="unidade" id="edit_unidade" required class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Quantidade *</label>
                    <input type="number" step="0.0001" name="quantidade" id="edit_qtd" required oninput="calcularTotal('edit_qtd', 'edit_vlr', 'edit_total')" class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Valor Unit. *</label>
                    <input type="number" step="0.01" name="valor_unitario" id="edit_vlr" required oninput="calcularTotal('edit_qtd', 'edit_vlr', 'edit_total')" class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-12">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Total Estimado</label>
                    <input type="text" id="edit_total" readonly class="w-full bg-gray-100 border-gray-300 rounded text-gray-500 font-bold">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                <button type="button" onclick="document.getElementById('modalEditarItem').close()" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow">Atualizar Item</button>
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

    function openModalEditarLote(id, nomeAtual) {
        const modal = document.getElementById('modalEditarLote');
        const form = document.getElementById('formEditarLote');
        const inputNome = document.getElementById('edit_lote_nome');

        form.action = "{{ url('lotes') }}/" + id;
        inputNome.value = nomeAtual;

        modal.showModal();
    }

    function openModalEditarItem(id, descricao, unidade, qtd, vlr) {
        const modal = document.getElementById('modalEditarItem');
        const form = document.getElementById('formEditarItem');
        
        form.action = "{{ url('itens') }}/" + id;

        document.getElementById('edit_descricao').value = descricao;
        document.getElementById('edit_unidade').value = unidade;
        document.getElementById('edit_qtd').value = qtd;
        document.getElementById('edit_vlr').value = vlr;

        calcularTotal('edit_qtd', 'edit_vlr', 'edit_total');

        modal.showModal();
    }

    function calcularTotal(idQtd, idVlr, idTotal) {
        const qtd = parseFloat(document.getElementById(idQtd).value) || 0;
        const vlr = parseFloat(document.getElementById(idVlr).value) || 0;
        const total = qtd * vlr;
        
        document.getElementById(idTotal).value = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
</script>
@endpush