<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VarianteProducto extends Model
{
    protected $table = 'variante_productos';

    protected $primaryKey = 'id_variante';

    public $timestamps = false;

    protected $fillable = [
        'color',
        'talla',
        'stock',
        'estadoActivo',
        'fechaCreacion',
        'id_producto',
    ];

    protected $casts = [
        'talla' => 'string',
        'stock' => 'integer',
        'estadoActivo' => 'boolean',
    ];


    public function producto(): BelongsTo
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto',
            'id_producto'
        );
    }

    public function itemsCarrito(): HasMany
    {
        return $this->hasMany(ItemCarrito::class, 'id_variante', 'id_variante');
    }

    public function itemsPedido(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'id_variante', 'id_variante');
    }
}
