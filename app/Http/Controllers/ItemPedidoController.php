<?php

namespace App\Http\Controllers;

use App\Models\ItemPedido;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ItemPedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json($this->consulta($request)->orderByDesc('id_item_pedido')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->exigirAdmin($request);
        $item = ItemPedido::create($this->validarDatos($request));

        return response()->json($item->load(['pedido', 'variante.producto']), 201);
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
        $item = ItemPedido::findOrFail($id);
        $item->update($this->validarDatos($request, true));

        return response()->json($item->load(['pedido', 'variante.producto']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $this->exigirAdmin($request);
        ItemPedido::findOrFail($id)->delete();

        return response()->json(['message' => 'Línea del pedido eliminada correctamente']);
    }

    private function consulta(Request $request): Builder
    {
        abort_unless($request->user()->estadoActivo, 403, 'Usuario inactivo.');
        $consulta = ItemPedido::with(['pedido', 'variante.producto']);

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
            'id_variante' => $editar ? ['prohibited'] : ['required', 'integer', 'exists:variante_productos,id_variante'],
            'cantidad' => [$editar ? 'sometimes' : 'required', 'required', 'integer', 'min:1'],
            'precioUnitario' => ['prohibited'],
            'subtotalLinea' => ['prohibited'],
        ]);
    }
}
