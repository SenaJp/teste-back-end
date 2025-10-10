@extends('layouts.app')

@section('title', 'Criar Produto')

@section('content')
<div class="mb-8">
    <div class="flex items-center">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Criar Produto</h1>
            <p class="text-gray-600 mt-2">Adicione um novo produto ao catálogo</p>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card p-8">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome do Produto *
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="form-input @error('name') border-red-500 @enderror"
                       placeholder="Digite o nome do produto" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="price_display" class="block text-sm font-medium text-gray-700 mb-2">
                    Preço *
                </label>
                <input type="text" id="price_display"
                       class="form-input @error('price') border-red-500 @enderror"
                       placeholder="R$ 0,00" inputmode="numeric" autocomplete="off" required>
                <input type="hidden" id="price" name="price" value="{{ old('price') }}">
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
                          placeholder="Descreva o produto" required>{{ old('description') }}</textarea>
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
                        <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
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
                <input type="url" id="image" name="image" value="{{ old('image') }}"
                       class="form-input @error('image') border-red-500 @enderror"
                       placeholder="https://exemplo.com/imagem.jpg">
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">URL opcional da imagem do produto</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Salvar Produto
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hiddenPrice = document.getElementById('price');
    const display = document.getElementById('price_display');
    if (!hiddenPrice || !display) return;

    const onlyDigits = (str) => (str || '').replace(/\D/g, '');
    const formatBR = (num) => Number(num).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function applyMaskFromDisplay() {
        const digits = onlyDigits(display.value);
        if (!digits) {
            display.value = '';
            hiddenPrice.value = '';
            return;
        }
        const val = parseInt(digits, 10) / 100;
        display.value = formatBR(val);
        hiddenPrice.value = val.toFixed(2);
    }

    if (hiddenPrice.value) {
        display.value = formatBR(hiddenPrice.value);
    }

    display.addEventListener('input', applyMaskFromDisplay);
    display.addEventListener('keyup', applyMaskFromDisplay);
    display.addEventListener('blur', applyMaskFromDisplay);

    const form = display.closest('form');
    if (form) {
        form.addEventListener('submit', applyMaskFromDisplay);
    }
});

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
