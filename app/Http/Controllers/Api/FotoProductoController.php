<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FotoProducto;
use Illuminate\Http\Request;

class FotoProductoController extends Controller
{
    // GET /api/fotos
    public function index()
    {
        $fotos = FotoProducto::with('producto')->get();

        return response()->json($fotos);
    }

    // GET /api/fotos/{id}
    public function show($id)
    {
        $foto = FotoProducto::with('producto')->find($id);

        if (!$foto) {
            return response()->json([
                'mensaje' => 'Foto no encontrada'
            ], 404);
        }

        return response()->json($foto);
    }

    // POST /api/fotos
    public function store(Request $request)
    {
        $datos = $request->validate([
            'urlFoto' => 'required|string|max:255',
            'orden' => 'required|integer|min:1',
            'estadoActivo' => 'required|boolean',
            'id_producto' => 'required|exists:productos,id_producto',
        ]);

        $foto = FotoProducto::create($datos);

        $foto->load('producto');

        return response()->json([
            'mensaje' => 'Foto creada correctamente',
            'foto' => $foto
        ], 201);
    }

    // PUT /api/fotos/{id}
    public function update(Request $request, $id)
    {
        $foto = FotoProducto::find($id);

        if (!$foto) {
            return response()->json([
                'mensaje' => 'Foto no encontrada'
            ], 404);
        }

        $datos = $request->validate([
            'urlFoto' => 'required|string|max:255',
            'orden' => 'required|integer|min:1',
            'estadoActivo' => 'required|boolean',
            'id_producto' => 'required|exists:productos,id_producto',
        ]);

        $foto->update($datos);

        $foto->load('producto');

        return response()->json([
            'mensaje' => 'Foto actualizada correctamente',
            'foto' => $foto
        ]);
    }

    // DELETE /api/fotos/{id}
    public function destroy($id)
    {
        $foto = FotoProducto::find($id);

        if (!$foto) {
            return response()->json([
                'mensaje' => 'Foto no encontrada'
            ], 404);
        }

        $foto->delete();

        return response()->json([
            'mensaje' => 'Foto eliminada correctamente'
        ]);
    }
}
