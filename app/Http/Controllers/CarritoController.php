<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Carrito::with('usuario')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_usuario' => 'required|exists:users,id|unique:carritos,id_usuario',
            'estadoActivo' => 'boolean',
        ]);

        $carrito = Carrito::create($datos);

        return response()->json($carrito, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $carrito = Carrito::with('usuario')->findOrFail($id);

        return response()->json($carrito);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $carrito = Carrito::findOrFail($id);

        $datos = $request->validate([
            'id_usuario' => [
                'sometimes',
                'required',
                'exists:users,id',
                Rule::unique('carritos', 'id_usuario')->ignore($carrito->id_carrito, 'id_carrito'),
            ],
            'estadoActivo' => 'boolean',
        ]);

        $carrito->update($datos);

        return response()->json($carrito);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $carrito = Carrito::findOrFail($id);

        $carrito->delete();

        return response()->json([
            'message' => 'Carrito eliminado correctamente',
        ]);
    }
}
