<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Categoria;



class CategoriaController extends Controller
{
    //GET ALL
    public function index()
    {
        $categorias = Categoria::all();

        return response()->json($categorias);
    }

    //GET BY ID
    public function show($id)
{
    $categoria = Categoria::find($id);

    if (!$categoria) {
        return response()->json([
            'mensaje' => 'Categoría no encontrada'
        ], 404);
    }

    return response()->json($categoria);
}
    //POST CREATE
    public function store(Request $request)
{
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'estadoActivo' => 'boolean',
    ]);

    $categoria = Categoria::create($datos);

    return response()->json([
        'mensaje' => 'Categoría creada correctamente',
        'categoria' => $categoria
    ], 201);
}
    ///PUT EDIT
    public function update(Request $request, $id)
{
    $categoria = Categoria::find($id);

    if (!$categoria) {
        return response()->json([
            'mensaje' => 'Categoría no encontrada'
        ], 404);
    }

    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'estadoActivo' => 'required|boolean',
    ]);

    $categoria->update($datos);

    return response()->json([
        'mensaje' => 'Categoría actualizada correctamente',
        'categoria' => $categoria
    ]);
}
    public function destroy($id)
{
    $categoria = Categoria::find($id);

    if (!$categoria) {
        return response()->json([
            'mensaje' => 'Categoría no encontrada'
        ], 404);
    }

    $categoria->delete();

    return response()->json([
        'mensaje' => 'Categoría eliminada correctamente'
    ]);
}
}
