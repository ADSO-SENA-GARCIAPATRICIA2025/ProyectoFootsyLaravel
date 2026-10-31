<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Pedido extends Model
{
    // Definimos los estados posibles del pedido y las transiciones permitidas entre ellos atraves de constantes de clase. Esto nos permite tener un control más estricto sobre el flujo de estados del pedido y evitar inconsistencias en la base de datos.
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'en_preparacion' => 'En preparación',
        'enviado' => 'Enviado',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];
    // Definimos las transiciones permitidas entre los estados del pedido atraves de un array  donde la clave es el estado actual y el valor es un array con los estados a los que se puede transicionar.
    public const TRANSICIONES = [
        'pendiente' => ['confirmado', 'cancelado'],
        'confirmado' => ['en_preparacion', 'cancelado'],
        'en_preparacion' => ['enviado', 'cancelado'],
        'enviado' => ['entregado'],
        'entregado' => [],
        'cancelado' => [],
    ];

    protected $table = 'pedidos';

    protected $primaryKey = 'id_pedido';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_direccion',
        'estadoPedido',
        'subtotal',
        'descuento',
        'impuestos',
        'costoEnvio',
    ];

    protected $attributes = [
        'estadoPedido' => 'pendiente',
        'descuento' => '0.00',
        'impuestos' => '0.00',
        'costoEnvio' => '0.00',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'costoEnvio' => 'decimal:2',
        'total' => 'decimal:2',
        'fechaCreacion' => 'datetime',
        'direccionEntrega' => 'array',
    ];

    public static function reglasImporte(): array
    {
        return ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'];
    }

    // Trabajamos en centavos enteros para no acumular errores al sumar decimales.
    public static function aCentavos(string $importe): int
    {
        [$entero, $decimales] = array_pad(explode('.', $importe, 2), 2, '');

        return ((int) $entero * 100) + (int) str_pad($decimales, 2, '0');
    }

    public static function desdeCentavos(int $centavos): string
    {
        return intdiv($centavos, 100) . '.' . str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }

    public function subtotalDesdeItems(): string
    {
        $centavos = 0;
        foreach ($this->items()->pluck('subtotalLinea') as $importe) {
            $centavos += static::aCentavos((string) $importe);
        }

        return static::desdeCentavos($centavos);
    }

    public function recalcularDesdeItems(): void
    {
        $this->subtotal = $this->subtotalDesdeItems();
        $this->save();
    }

    protected static function booted(): void
    {
        static::saving(function (Pedido $pedido): void {
            // Una vez hay detalle, la cabecera utiliza la suma de sus líneas.
            if ($pedido->exists && $pedido->items()->exists()) {
                $pedido->subtotal = $pedido->subtotalDesdeItems();
            }

            $datos = $pedido->getAttributes();
            Validator::make($datos, [
                'subtotal' => static::reglasImporte(),
                'descuento' => static::reglasImporte(),
                'impuestos' => static::reglasImporte(),
                'costoEnvio' => static::reglasImporte(),
                'estadoPedido' => ['required', Rule::in(array_keys(static::ESTADOS))],
            ])->validate();

            if ($pedido->exists) {
                if ($pedido->isDirty('estadoPedido') && ! in_array($pedido->estadoPedido,
                    static::TRANSICIONES[$pedido->getOriginal('estadoPedido')] ?? [], true)) {
                    throw ValidationException::withMessages(['estadoPedido' => 'El pedido no puede pasar a ese estado desde su estado actual.']);
                }

                if ($pedido->isDirty(['id_usuario', 'id_direccion'])) {
                    throw ValidationException::withMessages(['id_direccion' => 'El usuario y la dirección del pedido no se pueden cambiar.']);
                }

                if ($pedido->getOriginal('estadoPedido') !== 'pendiente'
                    && $pedido->isDirty(['subtotal', 'descuento', 'impuestos', 'costoEnvio'])) {
                    throw ValidationException::withMessages(['subtotal' => 'Solo se pueden modificar los importes de un pedido pendiente.']);
                }
            } else {
                if ($pedido->estadoPedido !== 'pendiente') {
                    throw ValidationException::withMessages(['estadoPedido' => 'Los pedidos se crean en estado pendiente.']);
                }

                Validator::make($pedido->only(['id_usuario', 'id_direccion']), [
                    'id_usuario' => ['required', 'integer', 'exists:users,id'],
                    'id_direccion' => [
                        'required', 'integer',
                        Rule::exists('direcciones', 'id_direccion')->where('id_usuario', $pedido->id_usuario)->where('estadoActivo', true),
                    ],
                ])->validate();

                $direccion = Direccion::findOrFail($pedido->id_direccion);
                $pedido->direccionEntrega = $direccion->only([
                    'nombreDestinatario', 'telefono', 'direccion', 'ciudad', 'departamento',
                    'codigoPostal', 'pais', 'indicaciones',
                ]);
                $pedido->numeroOrden = 'FT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(10));
            }

            $subtotal = static::aCentavos($pedido->subtotal);
            $descuento = static::aCentavos($pedido->descuento);

            if ($descuento > $subtotal) {
                throw ValidationException::withMessages(['descuento' => 'El descuento no puede superar el subtotal.']);
            }

            $total = $subtotal - $descuento + static::aCentavos($pedido->impuestos) + static::aCentavos($pedido->costoEnvio);

            if ($total > 999999999999) {
                throw ValidationException::withMessages(['subtotal' => 'El total supera el importe máximo permitido.']);
            }

            $pedido->total = static::desdeCentavos($total);
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'id_pedido', 'id_pedido');
    }
}
