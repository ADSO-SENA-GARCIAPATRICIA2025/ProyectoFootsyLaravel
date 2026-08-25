<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VarianteProductoController;
use App\Http\Controllers\Api\FotoProductoController;
use App\Http\Controllers\Api\UsuarioController;

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

/////USERS //////////////
Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
