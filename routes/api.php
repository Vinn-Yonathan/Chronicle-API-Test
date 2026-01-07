<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/products', [ProductController::class, 'add']);
Route::get('/products', [ProductController::class, 'getList']);

Route::get('/products/{id}', [ProductController::class, 'get'])->where('id', '[0-9]+');
Route::put('/products/{id}', [ProductController::class, 'update'])->where('id', '[0-9]+');
Route::delete('/products/{id}', [ProductController::class, 'delete'])->where('id', '[0-9]+');
