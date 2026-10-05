<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Direccion extends Model
{
    protected $table = 'direcciones';

    protected $primaryKey = 'id_direccion';

    public $timestamps = false;

    protected $fillable = [
        'nombreDestinatario',
        'telefono',
        'direccion',
        'ciudad',
        'departamento',
        'codigoPostal',
        'pais',
        'indicaciones',
        'esPrincipal',
        'estadoActivo',
        'id_usuario',
    ];

    protected $casts = [
        'esPrincipal' => 'boolean',
        'estadoActivo' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
