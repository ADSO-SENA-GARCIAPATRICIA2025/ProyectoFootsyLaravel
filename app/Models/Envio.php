<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Envio extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_transito' => 'En tránsito',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];

    protected $table = 'envios';

    protected $primaryKey = 'id_envio';

    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'transportadora',
        'numeroGuia',
        'estadoEnvio',
    ];

    protected $attributes = [
        'estadoEnvio' => 'pendiente',
    ];

    protected $casts = [
        'fechaCreacion' => 'datetime',
        'fechaEnvio' => 'datetime',
        'fechaEntrega' => 'datetime',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}
