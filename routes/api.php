<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\CartController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('v1')->group(function () {

        Route::post('/register', 'register');
        Route::post('/login', 'login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', 'logout');
            Route::apiResource('categories', CategoryController::class);

            Route::apiResource('products', ProductController::class)
            ->except('update');
            Route::post('/products/{product}/update', [ProductController::class,'update']);

            Route::apiResource('carts', CartController::class)
            ->except(['show', 'update', 'destroy']);

            Route::put('carts/{cartItem}', [CartController::class, 'update']);

            Route::delete('carts/{cartItem}', [CartController::class, 'destroy']);
        });

});
