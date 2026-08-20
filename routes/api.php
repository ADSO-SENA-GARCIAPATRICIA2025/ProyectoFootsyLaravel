<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;

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
