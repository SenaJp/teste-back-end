<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('profile', [AuthController::class, 'updateProfile']);

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('products', ProductController::class);

    Route::get('products/category/{categoryId}', [ProductController::class, 'byCategory']);
    Route::get('products/with-image', [ProductController::class, 'withImage']);
    Route::get('products/without-image', [ProductController::class, 'withoutImage']);

    Route::post('import/all', [ImportController::class, 'importAll']);
    Route::post('import/{id}', [ImportController::class, 'importSpecific']);
});
