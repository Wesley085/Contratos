@extends('layouts.app')

@section('page-title', 'Nova Entrega')
@section('page-subtitle', 'Selecione o contrato e registre a saída de materiais.')

@section('content')

    {{-- Feedback de Erro --}}
    @if (session('error'))
        <div class="p-4 mb-6 rounded-lg bg-red-50 border border-red-200 fade-in">
            <p class="text-sm font-medium text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}</p>
        </div>
    @endif

    {{-- 1. SELEÇÃO DE PREFEITURA E CONTRATO --}}
    <div class="bg-white shadow-sm rounded-xl p-6 mb-8 border border-gray-100"
         x-data="contractSelector({{ $prefeituras }})">
        
        <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-search text-[#115e59]"></i> Selecione o Contrato
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prefeitura</label>
                    <select x-model="selectedPrefeituraId" @change="changePrefeitura"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                    <option value="">-- Selecione uma Prefeitura --</option>
                    <template x-for="pref in prefeituras" :key="pref.id">
                        <option :value="pref.id" x-text="pref.nome"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato</label>
                <button type="button" @click="openModal" :disabled="!selectedPrefeituraId"
                    class="w-full flex justify-between items-center px-4 py-2.5 border border-gray-300 rounded-md shadow-sm bg-white text-left focus:ring-[#115e59] focus:border-[#115e59] disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!selectedContratoId" class="text-gray-600">-- Clique para Selecionar o Contrato --</span>
                    <span x-show="selectedContratoId" class="font-semibold text-[#115e59]">
                        {{ $contratoSelecionado ? 'Contrato Nº ' . $contratoSelecionado->numero_contrato . ' Selecionado' : '' }}
                    </span>
                    <i class="fas fa-external-link-alt text-gray-400"></i>
                </button>
            </div>

            {{-- MODAL DE CONTRATOS --}}
            <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="isModalOpen" @click="closeModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="isModalOpen" x-transition.scale.origin.bottom
                         class="inline-block align-bottom bg-gray-50 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                            <div class="sm:flex sm:items-start justify-between">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-xl leading-6 font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                                        <i class="fas fa-file-signature text-[#115e59]"></i> Contratos Disponíveis
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Selecione o contrato desejado abaixo para vincular à nova entrega.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="closeModal" class="text-gray-400 hover:text-gray-700 transition-colors focus:outline-none bg-gray-100 rounded-full p-2 w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="p-6 overflow-y-auto max-h-[60vh]">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="cont in filteredContratos" :key="cont.id">
                                    <div @click="selectContrato(cont.id)" 
                                         class="bg-white border border-gray-200 rounded-xl p-5 cursor-pointer hover:border-[#115e59] hover:shadow-md transition-all group relative flex flex-col justify-between"
                                         :class="{'border-[#115e59] ring-2 ring-[#115e59] ring-opacity-20': selectedContratoId == cont.id}">
                                        
                                        <div>
                                            <div class="flex justify-between items-start mb-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-bold bg-[#115e59]/10 text-[#115e59]">
                                                    Nº <span x-text="cont.numero_contrato" class="ml-1"></span>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-4" x-text="cont.objeto"></p>
                                        </div>
                                        
                                        <div class="mt-5 pt-3 border-t border-gray-100 flex justify-end">
                                            <span class="text-sm font-bold text-gray-400 group-hover:text-[#115e59] transition-colors flex items-center gap-1">
                                                Selecionar <i class="fas fa-arrow-right text-xs"></i>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="filteredContratos.length === 0">
                                    <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                                        <i class="fas fa-folder-open text-3xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 font-medium">Nenhum contrato encontrado para esta prefeitura.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. FORMULÁRIO DE ENTREGA --}}
    @if($contratoSelecionado)
        <form action="{{ route('entregas.store') }}" method="POST" enctype="multipart/form-data" id="formEntrega" class="fade-in">
            @csrf
            <input type="hidden" name="contrato_id" value="{{ $contratoSelecionado->id }}">

            {{-- SEÇÃO A: DADOS DO RECIBO --}}
            <div class="bg-white shadow-sm rounded-xl p-6 mb-8 border border-gray-100">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h3 class="font-bold text-gray-700"><i class="far fa-file-alt mr-2"></i>Dados do Recibo</h3>
                    <span class="text-xs text-gray-500">Preencha os detalhes da entrega antes de selecionar os itens.</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data da Entrega *</label>
                        <input type="date" name="data_entrega" value="{{ date('Y-m-d') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comprovante (PDF/Foto)</label>
                        <input type="file" name="comprovante" accept="image/*,.pdf"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#115e59] file:text-white hover:file:bg-[#0d4a46] transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="observacoes" rows="1" placeholder="Opcional..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]"></textarea>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO B: ITENS --}}
            <div class="mb-4 flex justify-between items-end">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Itens Disponíveis</h3>
                    <p class="text-sm text-gray-500">Informe a quantidade entregue para cada item.</p>
                </div>
                
                <button type="button" onclick="preencherTudo()" 
                    class="text-sm bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-4 py-2 rounded-lg border border-indigo-200 font-semibold transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-magic"></i> Preencher Tudo com Saldo
                </button>
            </div>

            {{-- Tabela com Separação Visual de Lotes --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-5/12">Descrição do Item</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Valor Un.</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Saldo Disp.</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Qtd. Entrega</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total (R$)</th>
                        </tr>
                    </thead>

                    @foreach($contratoSelecionado->lotes as $index => $lote)
                        <tbody class="divide-y divide-gray-200 bg-white">
                            
                            <tr class="bg-slate-100 {{ $index > 0 ? 'border-t-[12px] border-white' : '' }}">
                                <td colspan="5" class="px-6 py-3 text-sm font-bold text-slate-700 uppercase tracking-wide border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-layer-group text-[#115e59]"></i> 
                                        {{ $lote->nome }}
                                    </div>
                                </td>
                            </tr>

                            @foreach($lote->itens as $item)
                                @php
                                    $jaEntregue = $item->ja_entregue ?? 0;
                                    $saldo = $item->quantidade - $jaEntregue;
                                    $bloqueado = $saldo <= 0.0001; 
                                    $inputId = "qtd_" . $item->id;
                                    $totalId = "total_" . $item->id;
                                    $msgId   = "msg_" . $item->id;
                                @endphp

                                <tr class="group hover:bg-[#115e59]/5 transition-colors {{ $bloqueado ? 'bg-gray-50 opacity-60' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->descricao }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Contratado: <span class="font-mono">{{ number_format($item->quantidade, 2, ',', '.') }} {{ $item->unidade }}</span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-4 text-center text-sm text-gray-600 font-mono">
                                        R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}
                                        <input type="hidden" id="vlr_{{ $item->id }}" value="{{ $item->valor_unitario }}">
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $bloqueado ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ number_format($saldo, 2, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 relative">
                                        <input type="number" step="0.0001" min="0" max="{{ $saldo }}"
                                            name="itens[{{ $item->id }}]" 
                                            id="{{ $inputId }}"
                                            data-saldo="{{ $saldo }}"
                                            oninput="validarSaldo(this, {{ $item->id }})"
                                            class="input-quantidade block w-full text-center border-gray-300 rounded-lg shadow-sm focus:ring-[#115e59] focus:border-[#115e59] font-bold text-gray-800 disabled:bg-gray-200 transition-all"
                                            placeholder="0,00"
                                            {{ $bloqueado ? 'disabled' : '' }}>
                                        
                                        <div id="{{ $msgId }}" class="hidden absolute top-0 left-0 -mt-6 w-full text-center z-10">
                                            <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded shadow-lg">Máx: {{ number_format($saldo, 2, ',', '.') }}</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-right text-sm font-bold text-gray-900 font-mono" id="{{ $totalId }}">
                                        R$ 0,00
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#062F43] text-white font-bold rounded-lg shadow-lg hover:bg-[#084d6e] transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Confirmar Entrega
                </button>
            </div>
        </form>
    @else
        <div class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <i class="fas fa-arrow-up text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-500">Selecione uma prefeitura e um contrato acima para começar.</h3>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    function validarSaldo(input, id) {
        const saldoMax = parseFloat(input.getAttribute('data-saldo'));
        const valorUnitario = parseFloat(document.getElementById('vlr_' + id).value);
        let valorDigitado = parseFloat(input.value);
        const msgErro = document.getElementById('msg_' + id);
        const displayTotal = document.getElementById('total_' + id);

        if (isNaN(valorDigitado)) valorDigitado = 0;

        if (valorDigitado > saldoMax) {
            input.value = saldoMax; 
            valorDigitado = saldoMax;
            input.classList.add('border-red-500', 'bg-red-50', 'text-red-700');
            msgErro.classList.remove('hidden');
            setTimeout(() => {
                input.classList.remove('border-red-500', 'bg-red-50', 'text-red-700');
                msgErro.classList.add('hidden');
            }, 2000);
        }

        const total = valorDigitado * valorUnitario;
        displayTotal.innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function preencherTudo() {
        if(!confirm('Deseja preencher automaticamente todos os itens com o saldo total disponível?')) return;
        const inputs = document.querySelectorAll('.input-quantidade');
        inputs.forEach(input => {
            if (!input.disabled) {
                input.value = input.getAttribute('data-saldo');
                input.dispatchEvent(new Event('input'));
            }
        });
    }

    function contractSelector(allPrefeituras) {
        return {
            prefeituras: allPrefeituras,
            selectedPrefeituraId: '{{ $contratoSelecionado->prefeitura_id ?? "" }}',
            selectedContratoId: '{{ $contratoSelecionado->id ?? "" }}',
            filteredContratos: [],
            isModalOpen: false,
            init() {
                if(this.selectedPrefeituraId) {
                    this.updateContratos();
                    this.$nextTick(() => {
                        this.selectedContratoId = '{{ $contratoSelecionado->id ?? "" }}';
                    });
                }
            },
            changePrefeitura() {
                this.updateContratos();
                if (this.selectedContratoId) {
                    this.selectedContratoId = '';
                    window.location.href = "{{ route('entregas.create') }}";
                }
            },
            updateContratos() {
                const pref = this.prefeituras.find(p => p.id == this.selectedPrefeituraId);
                this.filteredContratos = pref ? pref.contratos : [];
            },
            openModal() {
                if (this.selectedPrefeituraId) {
                    this.isModalOpen = true;
                }
            },
            closeModal() {
                this.isModalOpen = false;
            },
            selectContrato(id) {
                this.selectedContratoId = id;
                this.closeModal();
                this.goToContrato();
            },
            goToContrato() {
                if (this.selectedContratoId) {
                    document.body.style.cursor = 'wait';
                    window.location.href = "{{ route('entregas.create') }}?contrato_id=" + this.selectedContratoId;
                }
            }
        }
    }
</script>
@endpush