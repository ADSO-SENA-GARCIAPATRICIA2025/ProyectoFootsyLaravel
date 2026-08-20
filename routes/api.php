<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;

//////CATEGORIAS//////////////////
//GET ALL
Route::get('/categorias', [CategoriaController::class, 'index']);
//GET BY ID
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);
///POST
Route::post('/categorias', [CategoriaController::class, 'store']);
///PUT
Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
///DELETE
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);



/////PRODUCTOS//////////////
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
