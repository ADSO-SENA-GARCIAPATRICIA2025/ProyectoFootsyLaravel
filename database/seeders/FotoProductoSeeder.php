<?php

namespace Database\Seeders;

use App\Models\FotoProducto;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class FotoProductoSeeder extends Seeder
{
    public function run(): void
    {
        $producto = Producto::where('codigoProducto', 'FT001')
            ->firstOrFail();

        $fotos = [
            [
                'urlFoto' => '/images/air-max-01.jpg',
                'orden' => 1,
            ],
            [
                'urlFoto' => '/images/air-max-02.jpg',
                'orden' => 2,
            ],
            [
                'urlFoto' => '/images/air-max-03.jpg',
                'orden' => 3,
            ],
        ];

        foreach ($fotos as $foto) {
            FotoProducto::updateOrCreate(
                [
                    'urlFoto' => $foto['urlFoto'],
                    'id_producto' => $producto->id_producto,
                ],
                [
                    'orden' => $foto['orden'],
                    'estadoActivo' => true,
                ]
            );
        }
    }
}
