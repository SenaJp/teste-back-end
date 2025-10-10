<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('/profile', 'profile')->name('profile');

    Route::view('/products', 'products.index')->name('products.index');

    Route::get('/products/{id}', function ($id) {
        return view('products.show', ['id' => $id]);
    })->name('products.show');

    Route::view('/categories', 'categories.index')->name('categories.index');

    Route::view('/import', 'import')->name('import');

});
