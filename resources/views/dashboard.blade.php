@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-2">Bem-vindo ao sistema de gerenciamento de biblioteca</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card p-6 bg-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">{{ $totalProducts ?? 0 }}</h3>
                <p class="text-blue-100">Total de Produtos</p>
            </div>
            <div class="text-3xl">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('products.index') }}" class="text-blue-100 hover:text-white font-medium">
                Ver Produtos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <div class="card p-6 bg-green-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">{{ $totalCategories ?? 0 }}</h3>
                <p class="text-green-100">Total de Categorias</p>
            </div>
            <div class="text-3xl">
                <i class="fas fa-tags"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('categories.index') }}" class="text-green-100 hover:text-white font-medium">
                Ver Categorias <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <div class="card p-6 bg-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">{{ $productsWithImage ?? 0 }}</h3>
                <p class="text-purple-100">Produtos com Imagem</p>
            </div>
            <div class="text-3xl">
                <i class="fas fa-image"></i>
            </div>
        </div>
    </div>

    <div class="card p-6 bg-orange-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">{{ $productsWithoutImage ?? 0 }}</h3>
                <p class="text-orange-100">Produtos sem Imagem</p>
            </div>
            <div class="text-3xl">
                <i class="fas fa-image-slash"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Produtos Recentes</h2>
    </div>
    <div class="p-6">
        @if(isset($recentProducts) && $recentProducts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentProducts as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach($product->categories as $category)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mr-1">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver Produtos</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600 mb-4">Comece criando seu primeiro produto ou importe da API.</p>
                <div class="space-x-4">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Criar Produto</a>
                    <a href="{{ route('import') }}" class="btn btn-outline">Importar da API</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
