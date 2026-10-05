<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    public function index()
    {
        return response()->json(
            Direccion::with('usuario')->get()
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombreDestinatario' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'codigoPostal' => 'nullable|string|max:20',
            'pais' => 'required|string|max:100',
            'indicaciones' => 'nullable|string',
            'esPrincipal' => 'boolean',
            'estadoActivo' => 'boolean',
            'id_usuario' => 'required|exists:users,id',
        ]);

        $direccion = Direccion::create($datos);

        return response()->json($direccion, 201);
    }

    public function show(int $id)
    {
        $direccion = Direccion::with('usuario')
            ->findOrFail($id);

        return response()->json($direccion);
    }

    public function update(Request $request, int $id)
    {
        $direccion = Direccion::findOrFail($id);

        $datos = $request->validate([
            'nombreDestinatario' => 'sometimes|required|string|max:150',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'sometimes|required|string|max:255',
            'ciudad' => 'sometimes|required|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'codigoPostal' => 'nullable|string|max:20',
            'pais' => 'sometimes|required|string|max:100',
            'indicaciones' => 'nullable|string',
            'esPrincipal' => 'boolean',
            'estadoActivo' => 'boolean',
            'id_usuario' => 'sometimes|required|exists:users,id',
        ]);

        $direccion->update($datos);

        return response()->json($direccion);
    }

    public function destroy(int $id)
    {
        $direccion = Direccion::findOrFail($id);

        $direccion->delete();

        return response()->json([
            'message' => 'Dirección eliminada correctamente'
        ]);
    }
}
