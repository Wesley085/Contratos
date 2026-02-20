@extends('layouts.app')

@section('page-title', 'Meu Perfil')
@section('page-subtitle', 'Gerencie suas informações pessoais e segurança.')

@section('content')

    {{-- FEEDBACK DE SUCESSO --}}
    @if (session('status') === 'profile-updated')
        <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 fade-in" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <p class="text-sm font-medium text-green-800"><i class="fas fa-check-circle mr-2"></i> Informações atualizadas com sucesso.</p>
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 fade-in" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <p class="text-sm font-medium text-green-800"><i class="fas fa-check-circle mr-2"></i> Senha alterada com sucesso.</p>
        </div>
    @endif

    <div class="space-y-6">

        {{-- 1. ATUALIZAR INFORMAÇÕES DO PERFIL --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-700">Informações do Perfil</h3>
                <p class="text-sm text-gray-500">Atualize as informações do seu perfil e endereço de e-mail.</p>
            </div>

            <form method="post" action="{{ route('profile.update') }}" class="max-w-xl">
                @csrf
                @method('patch')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#184ea4] focus:border-[#184ea4]">
                    @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#184ea4] focus:border-[#184ea4]">
                    @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-4 py-2 bg-[#184ea4] text-white font-bold rounded-lg shadow hover:bg-[#133b7a] transition-colors">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>

        {{-- 2. ATUALIZAR SENHA --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-700">Atualizar Senha</h3>
                <p class="text-sm text-gray-500">Certifique-se de que sua conta esteja usando uma senha longa e aleatória.</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="max-w-xl">
                @csrf
                @method('put')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#184ea4] focus:border-[#184ea4]">
                    @error('current_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#184ea4] focus:border-[#184ea4]">
                    @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#184ea4] focus:border-[#184ea4]">
                    @error('password_confirmation') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-4 py-2 bg-[#184ea4] text-white font-bold rounded-lg shadow hover:bg-[#133b7a] transition-colors">
                        Alterar Senha
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. DELETAR CONTA --}}
        <div class="bg-red-50 p-6 rounded-xl shadow-sm border border-red-100">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-red-800">Deletar Conta</h3>
                <p class="text-sm text-red-600">
                    Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente.
                </p>
            </div>

            <button onclick="document.getElementById('modalDeleteUser').showModal()" 
                class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg shadow hover:bg-red-700 transition-colors">
                Excluir Conta
            </button>
        </div>

    </div>

    {{-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO --}}
    <dialog id="modalDeleteUser" class="m-auto rounded-lg shadow-xl p-0 w-full max-w-md backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
        <div class="bg-white p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tem certeza que deseja excluir sua conta?</h3>
            <p class="text-sm text-gray-500 mb-6">
                Uma vez excluída, todos os dados serão perdidos permanentemente. Por favor, digite sua senha para confirmar.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="mb-4">
                    <input type="password" name="password" placeholder="Sua senha" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    @error('password', 'userDeletion') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalDeleteUser').close()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">
                        Excluir Permanentemente
                    </button>
                </div>
            </form>
        </div>
    </dialog>

@endsection