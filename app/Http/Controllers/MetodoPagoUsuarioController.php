<?php

namespace App\Http\Controllers;

use App\Models\MetodoPagoUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MetodoPagoUsuarioController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->metodosPagoUsuario()->with('metodoPago')->get()
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);
        $datos['id_usuario'] = $request->user()->id;
        $medio = MetodoPagoUsuario::create($datos);

        return response()->json($medio->refresh()->load('metodoPago'), 201);
    }

    public function show(Request $request, int $id)
    {
        return response()->json(
            $request->user()->metodosPagoUsuario()->with('metodoPago')->findOrFail($id)
        );
    }

    public function update(Request $request, int $id)
    {
        $medio = $request->user()->metodosPagoUsuario()->findOrFail($id);
        $medio->update($this->validarDatos($request, $medio));

        return response()->json($medio->load('metodoPago'));
    }

    public function destroy(Request $request, int $id)
    {
        $medio = $request->user()->metodosPagoUsuario()->findOrFail($id);
        $medio->delete();

        return response()->json(['message' => 'Medio de pago guardado eliminado correctamente']);
    }

    private function validarDatos(Request $request, ?MetodoPagoUsuario $medio = null): array
    {
        $presencia = $medio ? 'sometimes|required' : 'required';
        $datos = $request->validate([
            // La API obtiene el dueño desde la sesión, no desde el cuerpo enviado.
            'id_usuario' => 'prohibited',
            'id_metodo_pago' => $presencia . '|integer|exists:metodo_pagos,id_metodo_pago',
            'referenciaProveedor' => $presencia . '|string|max:100',
            'marca' => 'nullable|string|max:50',
            'ultimosCuatro' => ['nullable', 'string', 'regex:/^[0-9]{4}$/'],
            'esPrincipal' => 'boolean',
            'estadoActivo' => 'boolean',
        ]);

        $valores = array_merge($medio?->only(['id_metodo_pago', 'referenciaProveedor']) ?? [], $datos);
        $unica = Rule::unique('metodo_pago_usuarios', 'referenciaProveedor')
            ->where('id_usuario', $request->user()->id)
            ->where('id_metodo_pago', $valores['id_metodo_pago']);

        if ($medio) {
            $unica->ignore($medio->id_metodo_pago_usuario, 'id_metodo_pago_usuario');
        }

        Validator::make($valores, ['referenciaProveedor' => [$unica]], [
            'referenciaProveedor.unique' => 'Este medio de pago ya está guardado para el usuario.',
        ])->validate();

        return $datos;
    }
}
