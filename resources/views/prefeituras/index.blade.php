@extends('layouts.app')

@section('page-title', 'Clientes / Prefeituras')
@section('page-subtitle', 'Gerencie as prefeituras e órgãos públicos atendidos.')

@section('content')

    @if (session('success'))
        <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 slide-in">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fas fa-check-circle text-green-400"></i></div>
                <div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div>
            </div>
        </div>
    @endif

    <div class="overflow-hidden bg-white shadow-sm rounded-xl fade-in">
        {{-- HEADER --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-700">Prefeituras</h3>
            </div>
            <a href="{{ route('prefeituras.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-[#062F43] rounded-lg hover:bg-[#084d6e] hover:shadow-lg hover:-translate-y-0.5">
                <i class="fas fa-plus"></i> Novo Cliente
            </a>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">CNPJ</th>
                        <th class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Contratos</th>
                        <th class="px-6 py-3 text-xs font-bold text-center text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($prefeituras as $prefeitura)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                                        <i class="fas fa-landmark"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $prefeitura->nome }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($prefeitura->endereco, 30) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono" data-type="cnpj">
                                {{ $prefeitura->cnpj }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $prefeitura->contratos_count ?? $prefeitura->contratos->count() }} Contratos
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('prefeituras.edit', $prefeitura->id) }}"
                                        class="group p-2 rounded-lg text-amber-500 hover:bg-amber-50 transition-colors"
                                        title="Gerenciar Contratos">
                                         <i class="fas fa-folder-open text-lg"></i>
                                    </a>
                                    <form action="{{ route('prefeituras.destroy', $prefeitura->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza? Isso arquivará a prefeitura e seus contratos.');">
                                        @csrf @method('DELETE')
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
                                <p>Nenhuma prefeitura cadastrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($prefeituras, 'links'))
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $prefeituras->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function formatCNPJ(v){return v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,"$1.$2.$3/$4-$5");}
            document.querySelectorAll("td[data-type='cnpj']").forEach(el => {
                let t = el.innerText.trim().replace(/\D/g,'');
                if(t.length === 14) el.innerText = formatCNPJ(t);
            });
        });
    </script>
    @endpush
@endsection