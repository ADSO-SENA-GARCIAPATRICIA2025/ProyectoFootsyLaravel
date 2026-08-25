<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Database\Seeder;

class VarianteProductoSeeder extends Seeder
{
    public function run(): void
    {
        $variantes = [
            // FT001 - Air Max Running
            ['codigoProducto' => 'FT001', 'talla' => '38', 'color' => 'Negro', 'stock' => 5],
            ['codigoProducto' => 'FT001', 'talla' => '39', 'color' => 'Negro', 'stock' => 8],
            ['codigoProducto' => 'FT001', 'talla' => '40', 'color' => 'Negro', 'stock' => 6],
            ['codigoProducto' => 'FT001', 'talla' => '41', 'color' => 'Blanco', 'stock' => 4],

            // FT002 - Mercurial Vapor
            ['codigoProducto' => 'FT002', 'talla' => '40', 'color' => 'Negro/Rojo', 'stock' => 6],
            ['codigoProducto' => 'FT002', 'talla' => '41', 'color' => 'Negro/Rojo', 'stock' => 8],
            ['codigoProducto' => 'FT002', 'talla' => '42', 'color' => 'Negro/Rojo', 'stock' => 5],
            ['codigoProducto' => 'FT002', 'talla' => '43', 'color' => 'Negro/Rojo', 'stock' => 3],

            // FT003 - Ultraboost Training
            ['codigoProducto' => 'FT003', 'talla' => '39', 'color' => 'Negro', 'stock' => 4],
            ['codigoProducto' => 'FT003', 'talla' => '40', 'color' => 'Negro', 'stock' => 7],
            ['codigoProducto' => 'FT003', 'talla' => '41', 'color' => 'Gris', 'stock' => 6],
            ['codigoProducto' => 'FT003', 'talla' => '42', 'color' => 'Gris', 'stock' => 4],

            // FT004 - Samba Classic
            ['codigoProducto' => 'FT004', 'talla' => '38', 'color' => 'Blanco/Negro', 'stock' => 5],
            ['codigoProducto' => 'FT004', 'talla' => '39', 'color' => 'Blanco/Negro', 'stock' => 7],
            ['codigoProducto' => 'FT004', 'talla' => '40', 'color' => 'Blanco/Negro', 'stock' => 6],
            ['codigoProducto' => 'FT004', 'talla' => '41', 'color' => 'Negro', 'stock' => 3],

            // FT005 - Air Force 1
            ['codigoProducto' => 'FT005', 'talla' => '39', 'color' => 'Blanco', 'stock' => 8],
            ['codigoProducto' => 'FT005', 'talla' => '40', 'color' => 'Blanco', 'stock' => 10],
            ['codigoProducto' => 'FT005', 'talla' => '41', 'color' => 'Blanco', 'stock' => 7],
            ['codigoProducto' => 'FT005', 'talla' => '42', 'color' => 'Blanco', 'stock' => 5],

            // FT006 - Classic Leather Boot
            ['codigoProducto' => 'FT006', 'talla' => '40', 'color' => 'Marrón', 'stock' => 4],
            ['codigoProducto' => 'FT006', 'talla' => '41', 'color' => 'Marrón', 'stock' => 6],
            ['codigoProducto' => 'FT006', 'talla' => '42', 'color' => 'Marrón', 'stock' => 5],
            ['codigoProducto' => 'FT006', 'talla' => '43', 'color' => 'Negro', 'stock' => 3],

            // FT007 - Comfort Sandal
            ['codigoProducto' => 'FT007', 'talla' => '36', 'color' => 'Beige', 'stock' => 5],
            ['codigoProducto' => 'FT007', 'talla' => '37', 'color' => 'Beige', 'stock' => 7],
            ['codigoProducto' => 'FT007', 'talla' => '38', 'color' => 'Negro', 'stock' => 6],
            ['codigoProducto' => 'FT007', 'talla' => '39', 'color' => 'Negro', 'stock' => 4],

            // FT008 - Kids Runner
            ['codigoProducto' => 'FT008', 'talla' => '35', 'color' => 'Azul', 'stock' => 5],
        ];

        foreach ($variantes as $variante) {
            $producto = Producto::where(
                'codigoProducto',
                $variante['codigoProducto']
            )->firstOrFail();

            VarianteProducto::updateOrCreate(
                [
                    'id_producto' => $producto->id_producto,
                    'talla' => $variante['talla'],
                    'color' => $variante['color'],
                ],
                [
                    'stock' => $variante['stock'],
                    'estadoActivo' => true,
                ]
            );
        }
    }
}
