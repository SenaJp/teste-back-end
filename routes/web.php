<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'webRegister'])->name('register.post');

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $totalProducts = \App\Models\Product::count();
        $totalCategories = \App\Models\Category::count();
        $productsWithImage = \App\Models\Product::withImage()->count();
        $productsWithoutImage = \App\Models\Product::withoutImage()->count();
        $recentProducts = \App\Models\Product::with('categories')->latest()->take(5)->get();

        return view('dashboard', compact('totalProducts', 'totalCategories', 'productsWithImage', 'productsWithoutImage', 'recentProducts'));
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/products', function () {
        return view('products.index');
    })->name('products.index');

    Route::get('/products/{id}', function ($id) {
        return view('products.show', ['id' => $id]);
    })->name('products.show');

    Route::get('/categories', function () {
        return view('categories.index');
    })->name('categories.index');

    Route::get('/import', function () {
        return view('import');
    })->name('import');

});
