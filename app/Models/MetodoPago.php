<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    protected $table = 'metodo_pagos';

    protected $primaryKey = 'id_metodo_pago';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'proveedor',
        'descripcion',
        'estadoActivo',
    ];

    protected $casts = [
        'estadoActivo' => 'boolean',
        'fechaCreacion' => 'datetime',
    ];

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'id_metodo_pago', 'id_metodo_pago');
    }

    public function metodosPagoUsuario(): HasMany
    {
        return $this->hasMany(MetodoPagoUsuario::class, 'id_metodo_pago', 'id_metodo_pago');
    }
}
