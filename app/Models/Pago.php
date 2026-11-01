<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Pago extends Model
{
    // Definimos los estados posibles del pago y las transiciones permitidas entre ellos atraves de constantes de clase. Esto nos permite tener un control más estricto sobre el flujo de estados del pago y evitar inconsistencias en la base de datos.
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
        'cancelado' => 'Cancelado',
    ];

    public const TRANSICIONES = [
        'pendiente' => ['aprobado', 'rechazado', 'cancelado'],
        'aprobado' => [],
        'rechazado' => [],
        'cancelado' => [],
    ];

    protected $table = 'pagos';

    protected $primaryKey = 'id_pago';

    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_metodo_pago',
        'referenciaTransaccion',
        'monto',
        'estadoPago',
    ];

    protected $attributes = [
        'estadoPago' => 'pendiente',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fechaCreacion' => 'datetime',
        'fechaPago' => 'datetime',
    ];

    public static function reglasMonto(): array
    {
        return ['required', 'numeric', 'min:0.01', 'max:9999999999.99', 'decimal:0,2'];
    }

    public function save(array $options = [])
    {
        return $this->getConnection()->transaction(function () use ($options) {
            if (is_string($this->referenciaTransaccion) && trim($this->referenciaTransaccion) === '') {
                $this->referenciaTransaccion = null;
            }

            Validator::make($this->getAttributes(), [
                'id_pedido' => ['required', 'integer', 'exists:pedidos,id_pedido'],
                'id_metodo_pago' => ['required', 'integer', 'exists:metodo_pagos,id_metodo_pago'],
                'referenciaTransaccion' => ['nullable', 'string', 'max:255'],
                'monto' => static::reglasMonto(),
                'estadoPago' => ['required', Rule::in(array_keys(static::ESTADOS))],
            ])->validate();

            if ($this->exists && $this->isDirty(['id_pedido', 'id_metodo_pago', 'monto'])) {
                throw ValidationException::withMessages(['monto' => 'El pedido, método y monto quedan fijos. Cancela el intento pendiente y crea otro.']);
            }

            // El bloqueo del pedido serializa las aprobaciones para no superar su saldo.
            $pedido = Pedido::whereKey($this->id_pedido)->lockForUpdate()->firstOrFail();
            $actual = $this->exists ? static::whereKey($this->getKey())->lockForUpdate()->firstOrFail() : null;
            if ($actual) {
                if ($actual->estadoPago !== 'pendiente' && $this->isDirty(['estadoPago', 'referenciaTransaccion'])) {
                    throw ValidationException::withMessages(['estadoPago' => 'Un pago cerrado no se puede modificar.']);
                }
                if ($actual->estadoPago !== $this->estadoPago
                    && ! in_array($this->estadoPago, static::TRANSICIONES[$actual->estadoPago], true)) {
                    throw ValidationException::withMessages(['estadoPago' => 'La transición de estado no está permitida.']);
                }
            } else {
                if ($this->estadoPago !== 'pendiente') {
                    throw ValidationException::withMessages(['estadoPago' => 'Los pagos se crean en estado pendiente.']);
                }
                if (! MetodoPago::findOrFail($this->id_metodo_pago)->estadoActivo) {
                    throw ValidationException::withMessages(['id_metodo_pago' => 'Selecciona un método de pago activo.']);
                }
            }

            $referencia = Rule::unique('pagos', 'referenciaTransaccion')->where('id_metodo_pago', $this->id_metodo_pago);
            if ($actual) {
                $referencia->ignore($actual->getKey(), 'id_pago');
            }
            Validator::make($this->getAttributes(), ['referenciaTransaccion' => ['nullable', $referencia]])->validate();

            if (! $actual || ($actual->estadoPago === 'pendiente' && $this->estadoPago === 'aprobado')) {
                if ($pedido->estadoPedido === 'cancelado') {
                    throw ValidationException::withMessages(['id_pedido' => 'No se puede registrar ni aprobar un pago de un pedido cancelado.']);
                }
                $aprobados = 0;
                foreach ($pedido->pagos()->where('estadoPago', 'aprobado')->pluck('monto') as $monto) {
                    $aprobados += Pedido::aCentavos((string) $monto);
                }
                $saldo = Pedido::aCentavos($pedido->total) - $aprobados;
                if (Pedido::aCentavos($this->monto) > $saldo) {
                    throw ValidationException::withMessages(['monto' => 'El monto supera el saldo pendiente del pedido.']);
                }
            }

            $this->fechaPago = $this->estadoPago === 'aprobado' ? ($actual?->fechaPago ?? now()) : null;

            try {
                return parent::save($options);
            } catch (UniqueConstraintViolationException $exception) {
                throw ValidationException::withMessages(['referenciaTransaccion' => 'Esta referencia ya está registrada para el método de pago.']);
            }
        });
    }

    public function delete()
    {
        throw ValidationException::withMessages(['estadoPago' => 'Los pagos se conservan como historial. Cancela los intentos pendientes en lugar de eliminarlos.']);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago', 'id_metodo_pago');
    }
}
