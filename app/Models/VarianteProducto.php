<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function producto(): BelongsTo
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto',
            'id_producto'
        );
    }
}
