@extends('layouts.app')

@section('title', 'Perfil do Usuário')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Perfil do Usuário</h1>
    <p class="text-gray-600 mt-2">Gerencie suas informações pessoais</p>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Informações Pessoais</h2>
    </div>
    <div class="p-6">
        <div id="loadingSpinner" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-gray-600 mt-2">Carregando perfil...</p>
        </div>

        <form id="profileForm" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                    <input type="tel" id="phone" name="phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                    <input type="password" id="password" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a senha atual</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações da Conta</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Membro desde:</span>
                            <span class="font-medium text-gray-900" id="createdAt">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Última atualização:</span>
                            <span class="font-medium text-gray-900" id="updatedAt">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-green-600" id="userStatus">Ativo</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Salvar Alterações
                </button>
            </div>
        </form>

        <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let userProfile = null;

    async function loadProfile() {
        try {
            $('#loadingSpinner').show();
            $('#profileForm').hide();

            const response = await api.getProfile();
            userProfile = response;

            $('#name').val(userProfile.name);
            $('#email').val(userProfile.email);
            $('#phone').val(userProfile.phone || '');

            $('#userId').text(userProfile.id);
            $('#createdAt').text(new Date(userProfile.created_at).toLocaleDateString('pt-BR'));
            $('#updatedAt').text(new Date(userProfile.updated_at).toLocaleString('pt-BR'));

            $('#loadingSpinner').hide();
            $('#profileForm').show();
        } catch (error) {
            $('#loadingSpinner').hide();
            showNotification('Erro ao carregar perfil', 'error');
        }
    }

    $('#profileForm').submit(async function(e) {
        e.preventDefault();

        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val()
        };

        const password = $('#password').val();
        if (password) {
            const passwordConfirmation = $('#password_confirmation').val();

            if (password !== passwordConfirmation) {
                showNotification('As senhas não coincidem', 'error');
                return;
            }

            formData.password = password;
            formData.password_confirmation = passwordConfirmation;
        }

        try {
            await api.updateProfile(formData);
            showNotification('Perfil atualizado com sucesso!');

            $('#password').val('');
            $('#password_confirmation').val('');

            loadProfile();
        } catch (error) {
            if (error.message) {
                showNotification(error.message, 'error');
            } else {
                showNotification('Erro ao atualizar perfil', 'error');
            }
        }
    });

    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();

        if (password && confirmation && password !== confirmation) {
            $(this).addClass('border-red-500');
            $(this).removeClass('border-gray-300');
        } else {
            $(this).removeClass('border-red-500');
            $(this).addClass('border-gray-300');
        }
    });

    loadProfile();
});
</script>
@endsection
