<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'descripcion',
        'estadoActivo',
        'fechaCreacion',
    ];
    
    public function productos(): HasMany
{
    return $this->hasMany(
        Producto::class,
        'id_categoria',
        'id_categoria'
    );
}
}
