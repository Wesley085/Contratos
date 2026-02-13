@extends('layouts.app')

@section('page-title', 'Gerenciar Cliente')
@section('page-subtitle', $prefeitura->nome)

@section('content')

{{-- ALERTAS --}}
@if (session('success'))
    <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 fade-in">
        <p class="text-sm font-medium text-green-800"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 gap-8">
    
    {{-- DADOS DA PREFEITURA --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Dados Cadastrais</h3>
            <span class="text-xs text-gray-400">ID: {{ $prefeitura->id }}</span>
        </div>
        <div class="p-6">
            <form action="{{ route('prefeituras.update', $prefeitura->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" name="nome" value="{{ old('nome', $prefeitura->nome) }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                        <input type="text" name="cnpj" id="cnpj_edit" value="{{ old('cnpj', $prefeitura->cnpj) }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" name="endereco" value="{{ old('endereco', $prefeitura->endereco) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="text-sm text-[#115e59] hover:underline font-medium">Atualizar Dados da Prefeitura</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CONTRATOS --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden" x-data="{ showModal: false }">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-700">Contratos Vinculados</h3>
                <p class="text-sm text-gray-500">Gerencie os contratos para este cliente.</p>
            </div>
            <button @click="showModal = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#115e59] rounded-lg hover:bg-[#0d4a46] shadow-sm transition-all">
                <i class="fas fa-file-signature"></i> Novo Contrato
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase">Nº Contrato</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase">Objeto</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase">Vigência</th>
                        <th class="px-6 py-3 text-xs font-bold text-center text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($prefeitura->contratos as $contrato)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $contrato->numero_contrato }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($contrato->objeto, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                @if($contrato->data_inicio)
                                    {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }} 
                                    a 
                                    {{ \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('contratos.edit', $contrato->id) }}" class="text-[#115e59] hover:text-[#0d4a46] mr-3 font-bold">
                                    <i class="fas fa-box-open mr-1"></i> Gerenciar Lotes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Nenhum contrato cadastrado ainda. Clique em "Novo Contrato" para começar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL DE NOVO CONTRATO --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- Overlay --}}
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('contratos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="prefeitura_id" value="{{ $prefeitura->id }}">
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Adicionar Novo Contrato</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Número do Contrato *</label>
                                    <input type="text" name="numero_contrato" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Objeto (Descrição Resumida) *</label>
                                    <textarea name="objeto" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Data Início</label>
                                        <input type="date" name="data_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Data Fim</label>
                                        <input type="date" name="data_fim" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#115e59] text-base font-medium text-white hover:bg-[#0d4a46] sm:ml-3 sm:w-auto sm:text-sm">
                                Salvar
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cnpjEdit = document.getElementById('cnpj_edit');
        if (cnpjEdit && typeof IMask !== 'undefined') {
            IMask(cnpjEdit, { mask: '00.000.000/0000-00' });
        }
    });
</script>
@endpush
@endsection