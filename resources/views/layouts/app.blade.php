<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gerenciamento de Biblioteca')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#6B7280',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    @auth
    <div class="flex">
        <nav class="sidebar">
            <div class="p-6">
                <div class="text-center mb-8">
                    <h2 class="text-xl font-bold text-white">Sistema de Biblioteca</h2>
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white' : '' }}">
                            <i class="fas fa-home mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-gray-700 text-white' : '' }}">
                            <i class="fas fa-box mr-3"></i>
                            Produtos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-gray-700 text-white' : '' }}">
                            <i class="fas fa-tags mr-3"></i>
                            Categorias
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('import') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('import') ? 'bg-gray-700 text-white' : '' }}">
                            <i class="fas fa-download mr-3"></i>
                            Importar Produtos
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('profile') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('profile') ? 'bg-gray-700 text-white' : '' }}">
                            <i class="fas fa-user mr-3"></i>
                            Perfil
                        </a>
                    </li>
                    <li class="pt-4">
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Sair
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="main-content flex-1">
            @yield('content')
        </main>
    </div>
    @else
    <div class="min-h-screen flex items-center justify-center">
        @yield('content')
    </div>
    @endauth

    <script src="{{ asset('js/api.js') }}"></script>
    @if (session('api_token'))
    <script>
        (function() {
            var t = @json(session('api_token'));
            if (window.api && t) {
                window.api.setToken(t);
            }
        })();
    </script>
    @endif

    @guest
    <script>
        if (window.api) {
            window.api.setToken(null);
        } else {
            try { localStorage.removeItem('api_token'); } catch (e) {}
        }
    </script>
    @endguest

    <script>
        (function(){
            var f = document.getElementById('logoutForm');
            if (f) {
                f.addEventListener('submit', function(){
                    try { localStorage.removeItem('api_token'); } catch (e) {}
                });
            }
        })();
    </script>
    @yield('scripts')
</body>
</html>
