<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrito extends Model
{
    protected $table = 'carritos';

    protected $primaryKey = 'id_carrito';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'estadoActivo',
    ];

    protected $casts = [
        'estadoActivo' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemCarrito::class, 'id_carrito', 'id_carrito');
    }
}
