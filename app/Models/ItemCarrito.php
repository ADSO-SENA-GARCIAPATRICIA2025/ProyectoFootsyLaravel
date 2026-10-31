<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCarrito extends Model
{
    protected $table = 'item_carritos';

    protected $primaryKey = 'id_item_carrito';

    public $timestamps = false;

    protected $fillable = [
        'cantidad',
        'id_carrito',
        'id_variante',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fechaCreacion' => 'datetime',
    ];

    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'id_carrito', 'id_carrito');
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'id_variante', 'id_variante');
    }
}
