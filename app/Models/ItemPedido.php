<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ItemPedido extends Model
{
    protected $table = 'item_pedidos';

    protected $primaryKey = 'id_item_pedido';

    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_variante',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precioUnitario' => 'decimal:2',
        'subtotalLinea' => 'decimal:2',
    ];

    public function save(array $options = [])
    {
        // La línea y los importes del pedido se guardan o se revierten juntos.
        return $this->getConnection()->transaction(function () use ($options) {
            Validator::make($this->getAttributes(), [
                'id_pedido' => ['required', 'integer', 'exists:pedidos,id_pedido'],
                'id_variante' => ['required', 'integer', 'exists:variante_productos,id_variante'],
                'cantidad' => ['required', 'integer', 'min:1'],
            ])->validate();

            if ($this->exists && $this->isDirty(['id_pedido', 'id_variante'])) {
                throw ValidationException::withMessages(['id_variante' => 'El pedido y la variante no se pueden cambiar. Elimina la línea y crea otra.']);
            }

            $pedido = Pedido::whereKey($this->id_pedido)->lockForUpdate()->firstOrFail();
            $this->comprobarPedidoPendiente($pedido);
            $variante = VarianteProducto::with('producto')->findOrFail($this->id_variante);

            if (! $variante->estadoActivo || ! $variante->producto?->estadoActivo) {
                throw ValidationException::withMessages(['id_variante' => 'El producto y su variante deben estar activos.']);
            }

            $unica = Rule::unique('item_pedidos', 'id_variante')->where('id_pedido', $this->id_pedido);
            if ($this->exists) {
                $unica->ignore($this->getKey(), 'id_item_pedido');
            }

            Validator::make($this->getAttributes(), [
                'cantidad' => ['integer', 'min:1', 'max:' . $variante->stock],
                'id_variante' => [$unica],
            ], [
                'cantidad.max' => 'La cantidad supera el stock disponible de la variante.',
                'id_variante.unique' => 'Esta variante ya está en el pedido. Edita la cantidad de la línea existente.',
            ])->validate();

            $precio = $this->exists ? $this->getRawOriginal('precioUnitario') : $variante->producto->precioVenta;
            Validator::make(['precioUnitario' => $precio], [
                'precioUnitario' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            ])->validate();
            $this->precioUnitario = $precio;

            $subtotal = Pedido::aCentavos($this->precioUnitario) * (int) $this->cantidad;
            if ($subtotal > 999999999999) {
                throw ValidationException::withMessages(['cantidad' => 'El subtotal de la línea supera el importe máximo permitido.']);
            }
            $this->subtotalLinea = Pedido::desdeCentavos($subtotal);

            $guardado = parent::save($options);
            if ($guardado) {
                $pedido->recalcularDesdeItems();
            }

            return $guardado;
        });
    }

    public function delete()
    {
        if (! $this->exists) {
            return null;
        }

        return $this->getConnection()->transaction(function () {
            $pedido = Pedido::whereKey($this->id_pedido)->lockForUpdate()->firstOrFail();
            $this->comprobarPedidoPendiente($pedido);
            $eliminado = parent::delete();
            if ($eliminado) {
                $pedido->recalcularDesdeItems();
            }

            return $eliminado;
        });
    }

    private function comprobarPedidoPendiente(Pedido $pedido): void
    {
        if ($pedido->estadoPedido !== 'pendiente') {
            throw ValidationException::withMessages(['id_pedido' => 'Solo se pueden modificar las líneas de un pedido pendiente.']);
        }
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'id_variante', 'id_variante');
    }
}
