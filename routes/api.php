<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VarianteProductoController;
use App\Http\Controllers\Api\FotoProductoController;

//////CATEGORIAS//////////////////
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);
Route::post('/categorias', [CategoriaController::class, 'store']);
Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

/////PRODUCTOS//////////////
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

/////VARIANTEPRODUCTOS//////////////
Route::get('/variantes', [VarianteProductoController::class, 'index']);
Route::get('/variantes/{id}', [VarianteProductoController::class, 'show']);
Route::post('/variantes', [VarianteProductoController::class, 'store']);
Route::put('/variantes/{id}', [VarianteProductoController::class, 'update']);
Route::delete('/variantes/{id}', [VarianteProductoController::class, 'destroy']);

/////FOTO PRODUCTOS//////////////
Route::get('/fotos', [FotoProductoController::class, 'index']);
Route::get('/fotos/{id}', [FotoProductoController::class, 'show']);
Route::post('/fotos', [FotoProductoController::class, 'store']);
Route::put('/fotos/{id}', [FotoProductoController::class, 'update']);
Route::delete('/fotos/{id}', [FotoProductoController::class, 'destroy']);
