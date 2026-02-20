@extends('layouts.app')

@section('page-title', 'Minha Empresa')
@section('page-subtitle', 'Gerencie os dados da empresa emissora dos recibos.')

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
            <h3 class="text-xl font-bold text-gray-700">Dados da Empresa</h3>
            <p class="text-sm text-gray-500">Esta é a empresa que aparecerá no cabeçalho dos recibos/contratos.</p>
        </div>

        {{-- Botão de Criar --}}
        {{-- <a href="{{ route('empresas.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-[#062F43] rounded-lg hover:bg-[#084d6e] hover:shadow-lg hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            Nova Empresa
        </a> --}}
    </div>

    {{-- TABELA --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Empresa</th>
                    <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">CNPJ</th>
                    <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Endereço</th>
                    <th class="px-6 py-3 text-xs font-bold text-center text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($empresas as $empresa)
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- Razão Social e Logo --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden border border-gray-200">
                                    @if($empresa->logo_path)
                                        <img src="{{ asset('storage/' . $empresa->logo_path) }}" alt="Logo" class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-building text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $empresa->razao_social }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- CNPJ --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono" data-type="cnpj">
                            {{ $empresa->cnpj }}
                        </td>

                        {{-- Endereço --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{Str::limit($empresa->endereco, 40) }}
                        </td>

                        {{-- Ações --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('empresas.edit', $empresa->id) }}"
                                    class="group p-2 rounded-lg text-amber-500 hover:bg-amber-50 transition-colors"
                                    title="Editar Dados">
                                     <i class="fas fa-edit text-lg"></i>
                                </a>
                                
                                {{-- Delete --}}
                                <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover esta empresa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="group p-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <p>Nenhuma empresa cadastrada.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function formatCNPJ(value) {
            return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
        }
        document.querySelectorAll("td[data-type='cnpj']").forEach(function(el) {
            let text = el.innerText.trim();
            let onlyNumbers = text.replace(/\D/g, '');
            if (onlyNumbers.length === 14) el.innerText = formatCNPJ(onlyNumbers);
        });
    });
</script>
@endpush
@endsection