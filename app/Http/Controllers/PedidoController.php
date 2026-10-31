<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json($this->consulta($request)->orderByDesc('id_pedido')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->exigirAdmin($request);
        $datos = $this->validarDatos($request);
        $pedido = Pedido::create($datos);

        return response()->json($pedido->refresh()->load('usuario:id,name,email'), 201);
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
        $pedido = Pedido::findOrFail($id);
        $pedido->update($this->validarDatos($request, true));

        return response()->json($pedido->load('usuario:id,name,email'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $this->exigirAdmin($request);
        $pedido = Pedido::findOrFail($id);
        abort_unless(in_array($pedido->estadoPedido, ['pendiente', 'cancelado'], true), 422,
            'Solo se pueden eliminar pedidos pendientes o cancelados.');
        $pedido->delete();

        return response()->json(['message' => 'Pedido eliminado correctamente']);
    }

    private function consulta(Request $request): Builder
    {
        abort_unless($request->user()->estadoActivo, 403, 'Usuario inactivo.');

        $consulta = Pedido::with('usuario:id,name,email');

        if ($request->user()->rolUsuario !== 'admin') {
            $consulta->where('id_usuario', $request->user()->id);
        }

        return $consulta;
    }

    private function exigirAdmin(Request $request): void
    {
        abort_unless($request->user()->rolUsuario === 'admin' && $request->user()->estadoActivo,
            403, 'Esta operación corresponde al administrador. La creación desde el carrito se implementará con ItemPedido.');
    }

    private function validarDatos(Request $request, bool $editar = false): array
    {
        return $request->validate([
            'id_usuario' => $editar ? ['prohibited'] : ['required', 'integer', 'exists:users,id'],
            'id_direccion' => $editar ? ['prohibited'] : ['required', 'integer', 'exists:direcciones,id_direccion'],
            'numeroOrden' => ['prohibited'],
            'total' => ['prohibited'],
            'direccionEntrega' => ['prohibited'],
            'estadoPedido' => ['sometimes', 'required', Rule::in(array_keys(Pedido::ESTADOS))],
            'subtotal' => $editar ? ['sometimes', ...Pedido::reglasImporte()] : Pedido::reglasImporte(),
            'descuento' => ['sometimes', ...Pedido::reglasImporte()],
            'impuestos' => ['sometimes', ...Pedido::reglasImporte()],
            'costoEnvio' => ['sometimes', ...Pedido::reglasImporte()],
        ]);
    }
}
