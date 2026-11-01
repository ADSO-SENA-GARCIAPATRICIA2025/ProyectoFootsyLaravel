<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json($this->consulta($request)->orderByDesc('id_pago')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->exigirAdmin($request);
        $pago = Pago::create($this->validarDatos($request));

        return response()->json($pago->refresh()->load(['pedido', 'metodoPago']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        return response()->json($this->consulta($request)->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $this->exigirAdmin($request);
        $pago = Pago::findOrFail($id);
        $pago->update($this->validarDatos($request, true));

        return response()->json($pago->load(['pedido', 'metodoPago']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $this->exigirAdmin($request);
        // El modelo rechaza el borrado para conservar el historial.
        Pago::findOrFail($id)->delete();
    }

    private function consulta(Request $request): Builder
    {
        abort_unless($request->user()->estadoActivo, 403, 'Usuario inactivo.');
        $consulta = Pago::with(['pedido', 'metodoPago']);
        if ($request->user()->rolUsuario !== 'admin') {
            $consulta->whereHas('pedido', fn (Builder $query) => $query->where('id_usuario', $request->user()->id));
        }

        return $consulta;
    }

    private function exigirAdmin(Request $request): void
    {
        abort_unless($request->user()->rolUsuario === 'admin' && $request->user()->estadoActivo,
            403, 'Esta operación corresponde al administrador.');
    }

    private function validarDatos(Request $request, bool $editar = false): array
    {
        return $request->validate([
            'id_pedido' => $editar ? ['prohibited'] : ['required', 'integer', 'exists:pedidos,id_pedido'],
            'id_metodo_pago' => $editar ? ['prohibited'] : ['required', 'integer', 'exists:metodo_pagos,id_metodo_pago'],
            'monto' => $editar ? ['prohibited'] : Pago::reglasMonto(),
            'referenciaTransaccion' => ['nullable', 'string', 'max:255'],
            'estadoPago' => ['sometimes', 'required', Rule::in(array_keys(Pago::ESTADOS))],
            'fechaPago' => ['prohibited'],
            'fechaCreacion' => ['prohibited'],
        ]);
    }
}
