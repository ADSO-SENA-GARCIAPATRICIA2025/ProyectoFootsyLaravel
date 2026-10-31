<?php

namespace App\Http\Controllers;

use App\Models\ItemCarrito;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ItemCarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ItemCarrito::with(['carrito.usuario', 'variante.producto'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);
        $item = ItemCarrito::create($datos);

        return response()->json($item->load(['carrito.usuario', 'variante.producto']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return response()->json(
            ItemCarrito::with(['carrito.usuario', 'variante.producto'])->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $item = ItemCarrito::findOrFail($id);
        $datos = $this->validarDatos($request, $item);
        $item->update($datos);

        return response()->json($item->load(['carrito.usuario', 'variante.producto']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $item = ItemCarrito::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item del carrito eliminado correctamente']);
    }

    private function validarDatos(Request $request, ?ItemCarrito $item = null): array
    {
        // Al editar, los campos omitidos conservan su valor actual.
        $presencia = $item ? 'sometimes|required' : 'required';
        $datos = $request->validate([
            'id_carrito' => $presencia . '|integer|exists:carritos,id_carrito',
            'id_variante' => $presencia . '|integer|exists:variante_productos,id_variante',
            'cantidad' => $presencia . '|integer|min:1',
        ]);

        $valores = array_merge($item?->only(['id_carrito', 'id_variante', 'cantidad']) ?? [], $datos);
        $variante = VarianteProducto::findOrFail($valores['id_variante']);
        $unica = Rule::unique('item_carritos', 'id_variante')
            ->where('id_carrito', $valores['id_carrito']);

        if ($item) {
            $unica->ignore($item->id_item_carrito, 'id_item_carrito');
        }

        Validator::make($valores, [
            'id_variante' => [$unica],
            'cantidad' => ['integer', 'min:1', 'max:' . $variante->stock],
        ], [
            'id_variante.unique' => 'Esta variante ya existe en el carrito. Edita la cantidad del item existente.',
            'cantidad.max' => 'La cantidad supera el stock disponible de la variante seleccionada.',
        ])->validate();

        return $datos;
    }
}
