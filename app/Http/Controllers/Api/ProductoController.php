<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // GET /api/productos
    public function index()
    {
        $productos = Producto::with('categoria')->get();

        return response()->json($productos);
    }

    // GET /api/productos/{id}
    public function show($id)
    {
        $producto = Producto::with('categoria')->find($id);

        if (!$producto) {
            return response()->json([
                'mensaje' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json($producto);
    }

    // POST /api/productos
    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigoProducto' => 'required|string|max:50|unique:productos,codigoProducto',
            'nombreProducto' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'marca' => 'required|string|max:100',
            'precioVenta' => 'required|numeric|min:0',
            'estadoActivo' => 'required|boolean',
            'publicoObjetivo' => 'required|in:mujer,hombre,unisex,infantil',
            'id_categoria' => 'required|exists:categorias,id_categoria',
        ]);

        $producto = Producto::create($datos);

        $producto->load('categoria');

        return response()->json([
            'mensaje' => 'Producto creado correctamente',
            'producto' => $producto
        ], 201);
    }

    // PUT /api/productos/{id}
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'mensaje' => 'Producto no encontrado'
            ], 404);
        }

        $datos = $request->validate([
            'codigoProducto' => 'required|string|max:50|unique:productos,codigoProducto,' . $id . ',id_producto',
            'nombreProducto' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'marca' => 'required|string|max:100',
            'precioVenta' => 'required|numeric|min:0',
            'estadoActivo' => 'required|boolean',
            'publicoObjetivo' => 'required|in:mujer,hombre,unisex,infantil',
            'id_categoria' => 'required|exists:categorias,id_categoria',
        ]);

        $producto->update($datos);

        $producto->load('categoria');

        return response()->json([
            'mensaje' => 'Producto actualizado correctamente',
            'producto' => $producto
        ]);
    }

    // DELETE /api/productos/{id}
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'mensaje' => 'Producto no encontrado'
            ], 404);
        }

        $producto->delete();

        return response()->json([
            'mensaje' => 'Producto eliminado correctamente'
        ]);
    }
}
