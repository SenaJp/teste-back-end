@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="mb-8">
    <div class="flex items-center">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Produto</h1>
            <p class="text-gray-600 mt-2">Edite as informações do produto</p>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card p-8">
        <form method="POST" action="{{ route('products.update', $product->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome do Produto *
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                       class="form-input @error('name') border-red-500 @enderror"
                       placeholder="Digite o nome do produto" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Preço *
                </label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}"
                       class="form-input @error('price') border-red-500 @enderror"
                       placeholder="0.00" step="0.01" min="0" required>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descrição *
                </label>
                <textarea id="description" name="description" rows="4"
                          class="form-input @error('description') border-red-500 @enderror"
                          placeholder="Descreva o produto" required>{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Categoria *
                </label>
                <select id="category" name="category"
                        class="form-input @error('category') border-red-500 @enderror" required>
                    <option value="">Selecione uma categoria</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}"
                                {{ (old('category', $product->categories->first()->name ?? '') == $category->name) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                    <option value="nova_categoria" {{ old('category') == 'nova_categoria' ? 'selected' : '' }}>
                        + Nova categoria
                    </option>
                </select>
                <div id="new-category-input" class="mt-2 hidden">
                    <input type="text" name="new_category" placeholder="Nome da nova categoria" class="form-input">
                </div>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    URL da Imagem
                </label>
                <input type="url" id="image" name="image" value="{{ old('image', $product->image_url) }}"
                       class="form-input @error('image') border-red-500 @enderror"
                       placeholder="https://exemplo.com/imagem.jpg">
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">URL opcional da imagem do produto</p>

                @if($product->image_url)
                    <div class="mt-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="h-32 w-32 object-cover rounded-lg border">
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Atualizar Produto
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.getElementById('category').addEventListener('change', function() {
    const newCategoryInput = document.getElementById('new-category-input');
    if (this.value === 'nova_categoria') {
        newCategoryInput.classList.remove('hidden');
        newCategoryInput.querySelector('input').required = true;
    } else {
        newCategoryInput.classList.add('hidden');
        newCategoryInput.querySelector('input').required = false;
    }
});

@if(old('category') == 'nova_categoria')
    document.getElementById('new-category-input').classList.remove('hidden');
    document.getElementById('new-category-input').querySelector('input').required = true;
@endif
</script>
@endsection
