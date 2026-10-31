<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetodoPagoUsuario extends Model
{
    protected $table = 'metodo_pago_usuarios';
    protected $primaryKey = 'id_metodo_pago_usuario';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 'id_metodo_pago', 'referenciaProveedor', 'marca',
        'ultimosCuatro', 'esPrincipal', 'estadoActivo',
    ];

    protected $hidden = ['referenciaProveedor'];

    protected $attributes = ['esPrincipal' => false, 'estadoActivo' => true];

    protected $casts = [
        'esPrincipal' => 'boolean',
        'estadoActivo' => 'boolean',
        'fechaCreacion' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago', 'id_metodo_pago');
    }

    public function save(array $options = [])
    {
        // Se aplica tanto al guardar desde Filament como desde la API.
        return $this->getConnection()->transaction(function () use ($options) {
            User::whereKey($this->id_usuario)->lockForUpdate()->firstOrFail();

            if (! $this->estadoActivo) {
                $this->esPrincipal = false;
            }

            // Guardamos antes de desmarcar los anteriores: si falla, se revierte todo.
            $guardado = parent::save($options);

            if ($guardado && $this->esPrincipal) {
                static::where('id_usuario', $this->id_usuario)
                    ->where('id_metodo_pago_usuario', '!=', $this->getKey())
                    ->where('esPrincipal', true)
                    ->update(['esPrincipal' => false]);
            }

            return $guardado;
        });
    }
}
