<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;

class VarianteProductoController extends Controller
{
    // GET /api/variantes
    public function index()
    {
        $variantes = VarianteProducto::with('producto')->get();

        return response()->json($variantes);
    }

    // GET /api/variantes/{id}
    public function show($id)
    {
        $variante = VarianteProducto::with('producto')->find($id);

        if (!$variante) {
            return response()->json([
                'mensaje' => 'Variante no encontrada'
            ], 404);
        }

        return response()->json($variante);
    }

    // POST /api/variantes
    public function store(Request $request)
    {
        $datos = $request->validate([
            'color' => 'required|string|max:50',
            'talla' => 'required|in:35,36,37,38,39,40,41,42,43,44,45',
            'stock' => 'required|integer|min:0',
            'estadoActivo' => 'required|boolean',
            'id_producto' => 'required|exists:productos,id_producto',
        ]);

        $variante = VarianteProducto::create($datos);

        $variante->load('producto');

        return response()->json([
            'mensaje' => 'Variante creada correctamente',
            'variante' => $variante
        ], 201);
    }

    // PUT /api/variantes/{id}
    public function update(Request $request, $id)
    {
        $variante = VarianteProducto::find($id);

        if (!$variante) {
            return response()->json([
                'mensaje' => 'Variante no encontrada'
            ], 404);
        }

        $datos = $request->validate([
            'color' => 'required|string|max:50',
            'talla' => 'required|in:35,36,37,38,39,40,41,42,43,44,45',
            'stock' => 'required|integer|min:0',
            'estadoActivo' => 'required|boolean',
            'id_producto' => 'required|exists:productos,id_producto',
        ]);

        $variante->update($datos);

        $variante->load('producto');

        return response()->json([
            'mensaje' => 'Variante actualizada correctamente',
            'variante' => $variante
        ]);
    }

    // DELETE /api/variantes/{id}
    public function destroy($id)
    {
        $variante = VarianteProducto::find($id);

        if (!$variante) {
            return response()->json([
                'mensaje' => 'Variante no encontrada'
            ], 404);
        }

        $variante->delete();

        return response()->json([
            'mensaje' => 'Variante eliminada correctamente'
        ]);
    }
}
