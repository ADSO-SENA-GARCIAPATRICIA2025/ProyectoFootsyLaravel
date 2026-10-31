<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VarianteProductoController;
use App\Http\Controllers\Api\FotoProductoController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ItemCarritoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\MetodoPagoUsuarioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemPedidoController;

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


////Direcciones/////
Route::get('/direcciones', [DireccionController::class, 'index']);
Route::get('/direcciones/{id}', [DireccionController::class, 'show']);
Route::post('/direcciones', [DireccionController::class, 'store']);
Route::put('/direcciones/{id}', [DireccionController::class, 'update']);
Route::delete('/direcciones/{id}', [DireccionController::class, 'destroy']);

/////CARRITOS//////////////
Route::get('/carritos', [CarritoController::class, 'index']);
Route::get('/carritos/{id}', [CarritoController::class, 'show']);
Route::post('/carritos', [CarritoController::class, 'store']);
Route::put('/carritos/{id}', [CarritoController::class, 'update']);
Route::delete('/carritos/{id}', [CarritoController::class, 'destroy']);

/////ITEMS DEL CARRITO//////////////
Route::get('/item-carritos', [ItemCarritoController::class, 'index']);
Route::get('/item-carritos/{id}', [ItemCarritoController::class, 'show']);
Route::post('/item-carritos', [ItemCarritoController::class, 'store']);
Route::put('/item-carritos/{id}', [ItemCarritoController::class, 'update']);
Route::delete('/item-carritos/{id}', [ItemCarritoController::class, 'destroy']);

/////METODOS DE PAGO//////////////
Route::get('/metodo-pagos', [MetodoPagoController::class, 'index']);
Route::get('/metodo-pagos/{id}', [MetodoPagoController::class, 'show']);
Route::post('/metodo-pagos', [MetodoPagoController::class, 'store']);
Route::put('/metodo-pagos/{id}', [MetodoPagoController::class, 'update']);
Route::delete('/metodo-pagos/{id}', [MetodoPagoController::class, 'destroy']);

/////MEDIOS DE PAGO GUARDADOS DEL USUARIO//////////////
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/metodo-pago-usuarios', [MetodoPagoUsuarioController::class, 'index']);
    Route::get('/metodo-pago-usuarios/{id}', [MetodoPagoUsuarioController::class, 'show']);
    Route::post('/metodo-pago-usuarios', [MetodoPagoUsuarioController::class, 'store']);
    Route::put('/metodo-pago-usuarios/{id}', [MetodoPagoUsuarioController::class, 'update']);
    Route::delete('/metodo-pago-usuarios/{id}', [MetodoPagoUsuarioController::class, 'destroy']);

    /////PEDIDOS: CONSULTA DEL CLIENTE Y GESTION ADMINISTRATIVA//////////////
    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::put('/pedidos/{id}', [PedidoController::class, 'update']);
    Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']);

    /////DETALLES DE PEDIDO//////////////
    Route::get('/item-pedidos', [ItemPedidoController::class, 'index']);
    Route::get('/item-pedidos/{id}', [ItemPedidoController::class, 'show']);
    Route::post('/item-pedidos', [ItemPedidoController::class, 'store']);
    Route::put('/item-pedidos/{id}', [ItemPedidoController::class, 'update']);
    Route::delete('/item-pedidos/{id}', [ItemPedidoController::class, 'destroy']);
});
