@extends('layouts.app')

@section('page-title', 'Editar Entrega #' . $entrega->id)
@section('page-subtitle', 'Alterar quantidades ou corrigir informações.')

@section('content')

    @if (session('error'))
        <div class="p-4 mb-6 rounded-lg bg-red-50 border border-red-200 fade-in">
            <p class="text-sm font-medium text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('entregas.update', $entrega->id) }}" method="POST" enctype="multipart/form-data" class="fade-in">
        @csrf
        @method('PUT')

        {{-- INFO DO CONTRATO --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 mb-8 flex items-start gap-4">
            <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                <i class="fas fa-file-contract text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-blue-900 text-lg">Contrato: {{ $entrega->contrato->numero_contrato }}</h3>
                <p class="text-blue-700">{{ $entrega->contrato->prefeitura->nome }}</p>
                <p class="text-sm text-blue-600 mt-1">{{ Str::limit($entrega->contrato->objeto, 100) }}</p>
            </div>
        </div>

        {{-- SEÇÃO A: DADOS DO RECIBO --}}
        <div class="bg-white shadow-sm rounded-xl p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="font-bold text-gray-700"><i class="far fa-edit mr-2"></i>Dados do Recibo</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data da Entrega *</label>
                    <input type="date" name="data_entrega" value="{{ old('data_entrega', $entrega->data_entrega->format('Y-m-d')) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alterar Comprovante</label>
                    <input type="file" name="comprovante" accept="image/*,.pdf"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#115e59] file:text-white hover:file:bg-[#0d4a46]">
                    @if($entrega->comprovante_path)
                        <p class="text-xs text-green-600 mt-1"><i class="fas fa-check"></i> Arquivo atual existente.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="1" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">{{ old('observacoes', $entrega->observacoes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- SEÇÃO B: ITENS --}}
        <div class="mb-4 flex justify-between items-end">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Itens da Entrega</h3>
                <p class="text-sm text-gray-500">Zerar a quantidade removerá o item desta entrega.</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-5/12">Item</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Valor Un.</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Max. Editável</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Qtd. Entregue</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total (R$)</th>
                    </tr>
                </thead>

                @foreach($entrega->contrato->lotes as $index => $lote)
                    {{-- Estrutura separada por Lote --}}
                    <tbody class="divide-y divide-gray-200 bg-white">
                        
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
                                $totalJaEntregueGeral = $item->ja_entregue ?? 0; 
                                $qtdNestaEntrega = $entrega->itens->firstWhere('id', $item->id)->pivot->quantidade_entregue ?? 0;
                                $saldoParaEdicao = ($item->quantidade - $totalJaEntregueGeral) + $qtdNestaEntrega;
                                $bloqueado = $saldoParaEdicao <= 0.0001; 
                                $inputId = "qtd_" . $item->id;
                            @endphp

                            <tr class="group hover:bg-blue-50/50 transition-colors {{ $bloqueado ? 'bg-gray-50 opacity-60' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->descricao }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Contratado: {{ number_format($item->quantidade, 2, ',', '.') }} {{ $item->unidade }}
                                    </div>
                                </td>
                                
                                <td class="px-4 py-4 text-center text-sm text-gray-600 font-mono">
                                    R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}
                                    <input type="hidden" id="vlr_{{ $item->id }}" value="{{ $item->valor_unitario }}">
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800"
                                          title="Saldo Atual do Contrato + Qtd desta Entrega">
                                        {{ number_format($saldoParaEdicao, 2, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 relative">
                                    <input type="number" step="0.0001" min="0" max="{{ $saldoParaEdicao }}"
                                        name="itens[{{ $item->id }}]" 
                                        id="{{ $inputId }}"
                                        value="{{ $qtdNestaEntrega > 0 ? $qtdNestaEntrega : '' }}"
                                        data-saldo="{{ $saldoParaEdicao }}"
                                        oninput="validarSaldo(this, {{ $item->id }})"
                                        class="block w-full text-center border-gray-300 rounded-lg shadow-sm focus:ring-[#115e59] focus:border-[#115e59] font-bold text-gray-800 disabled:bg-gray-200"
                                        placeholder="0,00"
                                        {{ $bloqueado && $qtdNestaEntrega == 0 ? 'disabled' : '' }}>
                                    
                                    <div id="msg_{{ $item->id }}" class="hidden absolute top-0 left-0 -mt-6 w-full text-center z-10">
                                        <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded shadow-lg">Máx: {{ number_format($saldoParaEdicao, 2, ',', '.') }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-right text-sm font-bold text-gray-900 font-mono" id="total_{{ $item->id }}">
                                    R$ {{ number_format($qtdNestaEntrega * $item->valor_unitario, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @endforeach
            </table>
        </div>

        <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
            <a href="{{ route('entregas.show', $entrega->id) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                Cancelar
            </a>
            <button type="submit" class="px-8 py-3 bg-[#062F43] text-white font-bold rounded-lg shadow-lg hover:bg-[#084d6e] transform hover:-translate-y-1 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </div>
    </form>

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
</script>
@endpush