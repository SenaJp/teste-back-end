@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md mx-auto">
    <div class="card p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Entrar</h1>
            <p class="text-gray-600 mt-2">Acesse sua conta</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="form-input" placeholder="seu@email.com" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                <input type="password" id="password" name="password"
                       class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary w-full">
                Entrar
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-gray-600">Não tem uma conta?
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">Cadastre-se aqui</a>
            </p>
        </div>
    </div>
</div>
@endsection
