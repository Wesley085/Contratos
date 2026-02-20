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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prefeitura</label>
                <select x-model="selectedPrefeituraId" @change="updateContratos"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                    <option value="">-- Selecione uma Prefeitura --</option>
                    <template x-for="pref in prefeituras" :key="pref.id">
                        <option :value="pref.id" x-text="pref.nome"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato</label>
                <select x-model="selectedContratoId" @change="goToContrato" :disabled="!selectedPrefeituraId"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] disabled:bg-gray-100">
                    <option value="">-- Selecione o Contrato --</option>
                    <template x-for="cont in filteredContratos" :key="cont.id">
                        <option :value="cont.id" 
                                x-text="cont.numero_contrato + ' - ' + cont.objeto.substring(0, 60) + '...'"
                                :selected="cont.id == selectedContratoId"></option>
                    </template>
                </select>
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
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#115e59] file:text-white hover:file:bg-[#0d4a46]">
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
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
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
                        {{-- TBODY separado para cada lote permite controlar as bordas --}}
                        <tbody class="divide-y divide-gray-200 bg-white">
                            
                            {{-- Cabeçalho do Lote (Com borda grossa branca superior para separar do anterior) --}}
                            <tr class="bg-slate-200 {{ $index > 0 ? 'border-t-[12px] border-white' : '' }}">
                                <td colspan="5" class="px-6 py-3 text-sm font-bold text-slate-800 uppercase tracking-wide border-b border-slate-300">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-layer-group text-slate-500"></i> 
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

                                <tr class="group hover:bg-blue-50/50 transition-colors {{ $bloqueado ? 'bg-gray-50 opacity-60' : '' }}">
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
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#062F43] text-white font-bold rounded-lg shadow-lg hover:bg-[#084d6e] transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
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
    // Scripts mantidos (validarSaldo, preencherTudo, contractSelector)
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
            init() {
                if(this.selectedPrefeituraId) {
                    this.updateContratos();
                    this.$nextTick(() => {
                        this.selectedContratoId = '{{ $contratoSelecionado->id ?? "" }}';
                    });
                }
            },
            updateContratos() {
                const pref = this.prefeituras.find(p => p.id == this.selectedPrefeituraId);
                this.filteredContratos = pref ? pref.contratos : [];
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