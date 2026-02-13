@extends('layouts.app')

@section('page-title', 'Editar Empresa')
@section('page-subtitle', 'Atualize os dados da empresa.')

@section('content')
<div class="overflow-hidden bg-white shadow-sm rounded-xl fade-in">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h3 class="text-lg font-bold text-gray-700">Dados Cadastrais</h3>
    </div>

    <div class="p-6">
        <form action="{{ route('empresas.update', $empresa->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- RAZÃO SOCIAL --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="razao_social" class="block text-sm font-medium text-gray-700">Razão Social <span class="text-red-500">*</span></label>
                    <input type="text" name="razao_social" id="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}" required
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] transition-colors">
                </div>

                {{-- CNPJ --}}
                <div>
                    <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ <span class="text-red-500">*</span></label>
                    <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj', $empresa->cnpj) }}" required
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] transition-colors">
                </div>

                {{-- ENDEREÇO --}}
                <div class="col-span-1 md:col-span-2">
                    <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                    <input type="text" name="endereco" id="endereco" value="{{ old('endereco', $empresa->endereco) }}"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-[#115e59] focus:border-[#115e59] transition-colors">
                </div>

                {{-- LOGO ATUAL E UPLOAD --}}
                <div class="col-span-1 md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    
                    <div class="flex items-start gap-6">
                        @if($empresa->logo_path)
                            <div class="flex flex-col items-center">
                                <span class="text-xs text-gray-500 mb-1">Atual</span>
                                <div class="h-24 w-24 border border-gray-200 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $empresa->logo_path) }}" alt="Logo Atual" class="h-full w-full object-cover">
                                </div>
                            </div>
                        @endif

                        <div class="flex-1">
                            <label for="logo_path" class="block text-sm font-medium text-gray-700">Alterar Logo</label>
                            <input type="file" name="logo_path" id="logo_path" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-[#115e59] file:text-white
                                file:cursor-pointer hover:file:bg-[#0f292b] transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="flex justify-end mt-8 space-x-4 pt-4 border-t border-gray-100">
                <a href="{{ route('empresas.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-[#062F43] rounded-lg hover:bg-[#084d6e] hover:shadow-lg transition-all">
                    Atualizar Empresa
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cnpjInput = document.getElementById('cnpj');
        if (cnpjInput && typeof IMask !== 'undefined') {
            IMask(cnpjInput, { mask: '00.000.000/0000-00' });
        }
    });
</script>
@endpush
@endsection