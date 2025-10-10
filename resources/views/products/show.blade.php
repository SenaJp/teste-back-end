@extends('layouts.app')

@section('title', 'Detalhe do Produto')

@section('content')
<div class="mb-8">
    <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Voltar para lista
    </a>
    <h1 class="text-3xl font-bold text-gray-900 mt-3">Detalhe do Produto</h1>
    <p class="text-gray-600 mt-2">Informações completas do produto selecionado</p>
    </div>

<div id="productContainer" class="bg-white rounded-lg shadow-md p-6">
    <div id="loading" class="text-center py-10">
        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
        <p class="text-gray-600 mt-2">Carregando produto...</p>
    </div>
    <div id="content" style="display:none;"></div>
</div>
@endsection

@section('scripts')
<script>
$(async function() {
    const productId = {{ (int) $id }};
    try {
        const product = await api.getProduct(productId);
        renderProduct(product);
    } catch (error) {
        $('#productContainer').html('<div class="text-red-600">Erro ao carregar produto.</div>');
    }

    function renderProduct(product) {
        const categories = (product.categories || []).map(c => `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">${c.name}</span>`).join('');
        const price = `R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}`;
        const createdAt = product.created_at ? new Date(product.created_at).toLocaleDateString('pt-BR') : '-';

        const html = `
            <div class="flex flex-col md:flex-row gap-6">
                <div class="md:w-1/3">
                    ${product.image_url ?
                        `<img src="${product.image_url}" class="w-full rounded-lg shadow" alt="${product.name}">` :
                        `<div class=\"w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center\">\n                            <i class=\"fas fa-image text-gray-300 text-4xl\"></i>\n                        </div>`
                    }
                </div>
                <div class="md:w-2/3">
                    <h2 class="text-2xl font-semibold text-gray-900">${product.name}</h2>
                    <div class="mt-2 text-lg text-gray-700">${price}</div>
                    <div class="mt-4 text-gray-700 whitespace-pre-line">${product.description || ''}</div>

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase">Categorias</h3>
                        <div class="mt-2">${categories || '<span class="text-gray-500">Sem categorias</span>'}</div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-500">ID</div>
                            <div class="text-gray-900 font-medium">${product.id}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-500">Criado em</div>
                            <div class="text-gray-900 font-medium">${createdAt}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-500">Possui imagem</div>
                            <div class="text-gray-900 font-medium">${product.image_url ? 'Sim' : 'Não'}</div>
                        </div>
                    </div>

                    <div class="mt-8 flex space-x-3">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Voltar</a>
                        <button id="editBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Editar</button>
                        <button id="deleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Excluir</button>
                    </div>
                </div>
            </div>
        `;

        $('#loading').hide();
        $('#content').html(html).show();

        $('#editBtn').on('click', function() {
            window.location.href = '{{ route('products.index') }}';
        });

        $('#deleteBtn').on('click', function() {
            confirmAction('Tem certeza que deseja excluir este produto?', async () => {
                try {
                    await api.deleteProduct(product.id);
                    showNotification('Produto excluído com sucesso!');
                    window.location.href = '{{ route('products.index') }}';
                } catch (error) {
                    showNotification('Erro ao excluir produto', 'error');
                }
            });
        });
    }
});
</script>
@endsection
