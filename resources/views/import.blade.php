@extends('layouts.app')

@section('title', 'Importar Produtos')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Importar Produtos</h1>
            <p class="text-gray-600 mt-2">Impor­te produtos e categorias da FakeStore API para sua base.</p>
        </div>
        <a href="https://fakestoreapi.com" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
            <i class="fas fa-external-link-alt mr-2"></i> Documentação FakeStore API
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-1">Importar todos os produtos</h2>
            <p class="text-gray-600 mb-4">Busca todos os produtos na API e cria/atualiza registros locais. Produtos já importados serão ignorados.</p>
            <button id="btnImportAll" onclick="importAll(this)" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i> Importar Tudo
            </button>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-1">Importar produto específico</h2>
            <p class="text-gray-600 mb-4">Informe o ID do produto na API externa para importar apenas ele.</p>
            <div class="flex items-center gap-3">
                <input type="number" id="productId" min="1" placeholder="ID do produto" class="w-48 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button id="btnImportOne" onclick="importSpecific(this)" class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                    <i class="fas fa-download mr-2"></i> Importar
                </button>
            </div>
        </div>

        <div id="importResult" class="hidden bg-white rounded-lg shadow p-6">
            <div id="resultBox" class="p-4 rounded-lg">
                <h3 class="font-semibold mb-2" id="resultTitle"></h3>
                <div id="importMessage" class="text-sm"></div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Estatísticas</h2>
            <div class="space-y-3 text-gray-800">
                <div class="flex justify-between">
                    <span>Total de Produtos</span>
                    <span class="font-semibold">{{ \App\Models\Product::count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total de Categorias</span>
                    <span class="font-semibold">{{ \App\Models\Category::count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Produtos Importados</span>
                    <span class="font-semibold">{{ \App\Models\Product::whereNotNull('external_id')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Produtos Manuais</span>
                    <span class="font-semibold">{{ \App\Models\Product::whereNull('external_id')->count() }}</span>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-list mr-2"></i> Ver Produtos
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Dicas</h2>
            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                <li>Importações repetidas não duplicam produtos: utilizamos o external_id para evitar duplicatas.</li>
                <li>Cada produto importado vem com a categoria fornecida pela API externa.</li>
                <li>Você pode complementar ou editar os produtos após a importação.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showResult(type, title, html) {
    const box = document.getElementById('resultBox');
    const res = document.getElementById('importResult');
    const ttl = document.getElementById('resultTitle');
    const msg = document.getElementById('importMessage');
    ttl.textContent = title;
    msg.innerHTML = html;
    box.className = 'p-4 rounded-lg ' + (type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200');
    res.classList.remove('hidden');
}

async function importAll(btn) {
    const button = btn || document.getElementById('btnImportAll');
    const original = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Importando...';
    try {
        const data = await api.importAllProducts();
        showResult('success', 'Importação concluída', `
            <div>Importados: <strong>${data.imported || 0}</strong></div>
            <div>Ignorados: <strong>${data.skipped || 0}</strong></div>
            <div class="text-xs text-gray-500 mt-2">${new Date().toLocaleString()}</div>
        `);
        showNotification('Importação concluída com sucesso!');
    } catch (err) {
        showResult('error', 'Falha na importação', `<div>${err.message || 'Não foi possível importar.'}</div>`);
        showNotification('Erro na importação', 'error');
    } finally {
        button.disabled = false;
        button.innerHTML = original;
    }
}

async function importSpecific(btn) {
    const input = document.getElementById('productId');
    const id = (input.value || '').trim();
    if (!id) {
        input.focus();
        showNotification('Informe um ID de produto', 'error');
        return;
    }
    const button = btn || document.getElementById('btnImportOne');
    const original = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Importando...';
    try {
        const data = await api.importProduct(id);
        const name = (data.product && data.product.name) ? data.product.name : '—';
        showResult('success', 'Produto importado', `
            <div>Produto: <strong>${name}</strong></div>
            <div class="text-xs text-gray-500 mt-2">${new Date().toLocaleString()}</div>
        `);
        showNotification('Produto importado com sucesso!');

    } catch (err) {
        showResult('error', 'Falha ao importar produto', `<div>${err.message || 'Não foi possível importar.'}</div>`);
        showNotification('Erro ao importar produto', 'error');
    } finally {
        button.disabled = false;
        button.innerHTML = original;
    }
}
</script>
@endsection
