@extends('layouts.app')

@section('title', 'Lista de Categorias')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Lista de Categorias</h1>
    <p class="text-gray-600 mt-2">Gerencie todas as categorias da biblioteca</p>
</div>

<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="searchInput" placeholder="Buscar por nome..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <button id="searchBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Categorias</h2>
        <button id="createCategoryBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Nova Categoria
        </button>
    </div>
    <div class="p-6">
        <div id="loadingSpinner" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-gray-600 mt-2">Carregando categorias...</p>
        </div>
        <div id="categoriesTable" class="overflow-x-auto" style="display: none;"></div>
    </div>
</div>

<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-4">Nova Categoria</h3>
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">

                    <div class="mb-4">
                        <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                        <input type="text" id="categoryName" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label for="categoryDescription" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                        <textarea id="categoryDescription" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentCategories = [];

    async function loadCategories(params = {}) {
        try {
            $('#loadingSpinner').show();
            $('#categoriesTable').hide();

            const response = await api.getCategories(params);
            currentCategories = response.data || response;

            renderCategoriesTable(currentCategories);
            $('#loadingSpinner').hide();
            $('#categoriesTable').show();
        } catch (error) {
            $('#loadingSpinner').hide();
            showNotification('Erro ao carregar categorias', 'error');
        }
    }

    function renderCategoriesTable(categories) {
        if (!categories || categories.length === 0) {
            $('#categoriesTable').html(`
                <div class="text-center py-12">
                    <i class="fas fa-tags text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma categoria encontrada</h3>
                    <p class="text-gray-600 mb-4">Comece criando sua primeira categoria.</p>
                    <button id="createFirstCategory" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Criar Categoria
                    </button>
                </div>
            `);
            return;
        }

        let tableHTML = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produtos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
        `;

        categories.forEach(category => {
            const productsCount = category.products ? category.products.length : 0;

            tableHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${category.name}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">${category.description || '-'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            ${productsCount} produto${productsCount !== 1 ? 's' : ''}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${new Date(category.created_at).toLocaleDateString('pt-BR')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="editCategory(${category.id})" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCategory(${category.id})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;

        $('#categoriesTable').html(tableHTML);
    }


    window.editCategory = async function(id) {
        try {
            const category = await api.getCategory(id);

            $('#modalTitle').text('Editar Categoria');
            $('#categoryId').val(category.id);
            $('#categoryName').val(category.name);
            $('#categoryDescription').val(category.description || '');

            $('#categoryModal').removeClass('hidden');
        } catch (error) {
            showNotification('Erro ao carregar categoria', 'error');
        }
    };

    window.deleteCategory = function(id) {
        confirmAction('Tem certeza que deseja excluir esta categoria?', async () => {
            try {
                await api.deleteCategory(id);
                showNotification('Categoria excluída com sucesso!');
                loadCategories();
            } catch (error) {
                showNotification('Erro ao excluir categoria', 'error');
            }
        });
    };

    $('#createCategoryBtn, #createFirstCategory').click(function() {
        $('#modalTitle').text('Nova Categoria');
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        $('#categoryModal').removeClass('hidden');
    });

    $('#cancelBtn').click(function() {
        $('#categoryModal').addClass('hidden');
    });

    $('#categoryForm').submit(async function(e) {
        e.preventDefault();

        const formData = {
            name: $('#categoryName').val(),
            description: $('#categoryDescription').val() || null
        };

        try {
            const categoryId = $('#categoryId').val();

            if (categoryId) {
                await api.updateCategory(categoryId, formData);
                showNotification('Categoria atualizada com sucesso!');
            } else {
                await api.createCategory(formData);
                showNotification('Categoria criada com sucesso!');
            }

            $('#categoryModal').addClass('hidden');
            loadCategories();
        } catch (error) {
            showNotification('Erro ao salvar categoria', 'error');
        }
    });

    $('#searchBtn').click(function() {
        const params = {};
        const searchTerm = $('#searchInput').val();

        if (searchTerm) params.name = searchTerm;

        loadCategories(params);
    });

    $('#categoryModal').click(function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });

    loadCategories();
});
</script>
@endsection
