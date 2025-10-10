@extends('layouts.app')

@section('title', 'Lista de Produtos')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Lista de Produtos</h1>
    <p class="text-gray-600 mt-2">Gerencie todos os produtos da loja</p>
</div>

<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="searchInput" placeholder="Buscar por nome..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas as categorias</option>
                </select>
                <button id="searchBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Produtos</h2>
        <button id="createProductBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Novo Produto
        </button>
    </div>
    <div class="p-6">
        <div id="loadingSpinner" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-gray-600 mt-2">Carregando produtos...</p>
        </div>
        <div id="productsTable" class="overflow-x-auto" style="display: none;"></div>
        <div id="pagination" class="mt-6 flex justify-center"></div>
    </div>
</div>

<div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-4">Novo Produto</h3>
                <form id="productForm">
                    <input type="hidden" id="productId">

                    <div class="mb-4">
                        <label for="productName" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                        <input type="text" id="productName" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-4">
                        <label for="productPrice" class="block text-sm font-medium text-gray-700 mb-2">Preço</label>
                        <input type="text" id="productPrice" inputmode="numeric" placeholder="0,00" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-4">
                        <label for="productDescription" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                        <textarea id="productDescription" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="productImageUrl" class="block text-sm font-medium text-gray-700 mb-2">URL da Imagem</label>
                        <input type="url" id="productImageUrl"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label for="productCategories" class="block text-sm font-medium text-gray-700 mb-2">Categorias</label>
            <select id="productCategories" multiple
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></select>
                        <p class="text-xs text-gray-500 mt-1">Segure Ctrl para selecionar múltiplas categorias</p>
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
    let categories = [];
    let currentProducts = [];

    async function loadCategories() {
        try {
            const response = await api.getCategories();
            categories = response.data || response;

            const categorySelect = $('#categoryFilter');
            const modalCategorySelect = $('#productCategories');

            categorySelect.empty().append('<option value="">Todas as categorias</option>');
            modalCategorySelect.empty();

            categories.forEach(category => {
                categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                modalCategorySelect.append(`<option value="${category.id}">${category.name}</option>`);
            });
        } catch (error) {
            showNotification('Erro ao carregar categorias', 'error');
        }
    }

    async function loadProducts(params = {}) {
        try {
            $('#loadingSpinner').show();
            $('#productsTable').hide();

            const response = await api.getProducts(params);
            currentProducts = response.data || response;

            renderProductsTable(currentProducts);
            $('#loadingSpinner').hide();
            $('#productsTable').show();
        } catch (error) {
            $('#loadingSpinner').hide();
            showNotification('Erro ao carregar produtos', 'error');
        }
    }

    function renderProductsTable(products) {
        if (!products || products.length === 0) {
            $('#productsTable').html(`
                <div class="text-center py-12">
                    <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                    <p class="text-gray-600 mb-4">Comece criando seu primeiro produto ou importe da API.</p>
                    <div class="space-x-4">
                        <button id="createFirstProduct" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Criar Produto
                        </button>
                        <a href="{{ route('import') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Importar da API
                        </a>
                    </div>
                </div>
            `);
            return;
        }

        let tableHTML = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
        `;

        products.forEach(product => {
            const categories = product.categories || [];
            const categoryNames = categories.map(cat => cat.name).join(', ');

            tableHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ${product.image_url ?
                                `<img class="h-10 w-10 rounded-lg object-cover mr-3" src="${product.image_url}" alt="${product.name}">` :
                                `<div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>`
                            }
                            <div>
                                <div class="text-sm font-medium text-gray-900">${product.name}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">${product.description.substring(0, 50)}${product.description.length > 50 ? '...' : ''}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${categories.map(cat =>
                            `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">${cat.name}</span>`
                        ).join('')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${new Date(product.created_at).toLocaleDateString('pt-BR')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewProduct(${product.id})" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editProduct(${product.id})" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900">
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

        $('#productsTable').html(tableHTML);
    }

    window.viewProduct = async function(id) {
        window.location.href = `/products/${id}`;
    };

    window.editProduct = async function(id) {
        try {
            const product = await api.getProduct(id);

            $('#modalTitle').text('Editar Produto');
            $('#productId').val(product.id);
            $('#productName').val(product.name);
            try {
                const formatted = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(product.price));
                $('#productPrice').val(formatted);
            } catch (e) {
                $('#productPrice').val(product.price);
            }
            $('#productDescription').val(product.description);
            $('#productImageUrl').val(product.image_url || '');

            const categoryIds = product.categories ? product.categories.map(cat => cat.id) : [];
            $('#productCategories').val(categoryIds);

            $('#productModal').removeClass('hidden');
        } catch (error) {
            showNotification('Erro ao carregar produto', 'error');
        }
    };

    window.deleteProduct = function(id) {
        confirmAction('Tem certeza que deseja excluir este produto?', async () => {
            try {
                await api.deleteProduct(id);
                showNotification('Produto excluído com sucesso!');
                loadProducts();
            } catch (error) {
                showNotification('Erro ao excluir produto', 'error');
            }
        });
    };

    $('#createProductBtn, #createFirstProduct').click(function() {
        $('#modalTitle').text('Novo Produto');
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#productModal').removeClass('hidden');
    });

    $('#cancelBtn').click(function() {
        $('#productModal').addClass('hidden');
    });

    $('#productForm').submit(async function(e) {
        e.preventDefault();
        const priceDisplay = $('#productPrice').val() || '';
        const normalizedPrice = (priceDisplay)
            .toString()
            .replace(/\s/g, '')
            .replace(/\./g, '')
            .replace(/,/g, '.')
            .replace(/[^0-9.]/g, '');
        const priceNumber = parseFloat(normalizedPrice);

        const formData = {
            name: $('#productName').val(),
            price: isNaN(priceNumber) ? 0 : priceNumber,
            description: $('#productDescription').val(),
            image_url: $('#productImageUrl').val() || null,
            categories: $('#productCategories').val().map(id => parseInt(id))
        };

        try {
            const productId = $('#productId').val();

            if (productId) {
                await api.updateProduct(productId, formData);
                showNotification('Produto atualizado com sucesso!');
            } else {
                await api.createProduct(formData);
                showNotification('Produto criado com sucesso!');
            }

            $('#productModal').addClass('hidden');
            loadProducts();
        } catch (error) {
            showNotification('Erro ao salvar produto', 'error');
        }
    });

    $('#searchBtn').click(function() {
        const params = {};
        const searchTerm = $('#searchInput').val();
        const categoryId = $('#categoryFilter').val();

        if (searchTerm) params.name = searchTerm;
        if (categoryId) params.category_id = categoryId;

        loadProducts(params);
    });

    $('#productModal').click(function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });

    const $price = $('#productPrice');
    function maskBRLInput($el) {
        let digits = ($el.val() || '').toString().replace(/\D/g, '');
        if (!digits) {
            $el.val('');
            return;
        }
        const value = (parseInt(digits, 10) / 100);
        const formatted = value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        $el.val(formatted);
    }
    $price.on('input', function() { maskBRLInput($price); });

    loadCategories();
    loadProducts();
});
</script>
@endsection
