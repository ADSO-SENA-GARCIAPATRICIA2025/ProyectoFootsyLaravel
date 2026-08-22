<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoProducto extends Model
{
    protected $table = 'foto_productos';
    protected $primaryKey = 'id_foto';

    public $timestamps = false;

    protected $fillable = [

        'urlFoto',
        'orden',
        'estadoActivo',
        'fechaCreacion',
        'id_producto'
    ];

      public function producto(): BelongsTo
    {
        return $this ->belongsTo(
            Producto::class,
            'id_producto',
            'id_producto'
        );
    }



}
