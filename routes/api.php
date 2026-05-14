<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductUpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{sku}', [ProductController::class,'show']);
});

Route::middleware(['auth:sanctum', 'throttle:600,1'])
    ->prefix('products/{sku}')
    ->group(function () {
        Route::patch('/price', [ProductUpdateController::class, 'updatePrice']);
        Route::patch('/stock', [ProductUpdateController::class, 'updateStock']);
        Route::patch('/description', [ProductUpdateController::class, 'updateDescription']);
        Route::patch('/images', [ProductUpdateController::class, 'updateImages']);
        Route::patch('/tags', [ProductUpdateController::class, 'updateTags']);
    });