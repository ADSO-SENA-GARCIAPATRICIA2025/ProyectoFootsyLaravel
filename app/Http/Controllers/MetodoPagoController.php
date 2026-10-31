<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(MetodoPago::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'proveedor' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'estadoActivo' => 'boolean',
        ]);

        $metodoPago = MetodoPago::create($datos);

        return response()->json($metodoPago->refresh(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return response()->json(MetodoPago::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $metodoPago = MetodoPago::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'proveedor' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'estadoActivo' => 'boolean',
        ]);

        $metodoPago->update($datos);

        return response()->json($metodoPago);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $metodoPago = MetodoPago::findOrFail($id);
        $metodoPago->delete();

        return response()->json([
            'message' => 'Metodo de pago eliminado correctamente',
        ]);
    }
}
