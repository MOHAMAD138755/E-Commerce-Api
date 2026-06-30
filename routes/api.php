<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('v1')->group(function () {

        Route::post('/register', 'register');
        Route::post('/login', 'login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', 'logout');
            Route::apiResource('categories', CategoryController::class);
        });

});
