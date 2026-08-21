<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = [
        'codigoProducto',
        'nombreProducto',
        'descripcion',
        'marca',
        'precioVenta',
        'estadoActivo',
        'fechaCreacion',
        'publicoObjetivo',
        'id_categoria',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            Categoria::class,
            'id_categoria',
            'id_categoria'
        );
    }

    public function variantes(): HasMany
{
    return $this->hasMany(
        VarianteProducto::class,
        'id_producto',
        'id_producto'
    );
}
}
