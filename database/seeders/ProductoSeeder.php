<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'codigoProducto' => 'FT001',
                'nombreProducto' => 'Air Max Running',
                'descripcion' => 'Zapatilla deportiva ligera diseñada para running',
                'marca' => 'Nike',
                'precioVenta' => 129.990,
                'publicoObjetivo' => 'mujer',
                'categoria' => 'Running',
            ],
            [
                'codigoProducto' => 'FT002',
                'nombreProducto' => 'Mercurial Vapor',
                'descripcion' => 'Calzado deportivo diseñado para la práctica del fútbol',
                'marca' => 'Nike',
                'precioVenta' => 159.990,
                'publicoObjetivo' => 'hombre',
                'categoria' => 'Fútbol',
            ],
            [
                'codigoProducto' => 'FT003',
                'nombreProducto' => 'Ultraboost Training',
                'descripcion' => 'Zapatilla cómoda y estable para entrenamiento y gimnasio',
                'marca' => 'Adidas',
                'precioVenta' => 149.990,
                'publicoObjetivo' => 'hombre',
                'categoria' => 'Training',
            ],
            [
                'codigoProducto' => 'FT004',
                'nombreProducto' => 'Samba Classic',
                'descripcion' => 'Zapatilla de estilo clásico para uso diario',
                'marca' => 'Adidas',
                'precioVenta' => 109.990,
                'publicoObjetivo' => 'unisex',
                'categoria' => 'Casual',
            ],
            [
                'codigoProducto' => 'FT005',
                'nombreProducto' => 'Air Force 1',
                'descripcion' => 'Zapatilla deportiva de estilo urbano y casual',
                'marca' => 'Nike',
                'precioVenta' => 119.990,
                'publicoObjetivo' => 'unisex',
                'categoria' => 'Deportivo',
            ],
            [
                'codigoProducto' => 'FT006',
                'nombreProducto' => 'Classic Leather Boot',
                'descripcion' => 'Bota de cuero resistente para uso diario',
                'marca' => 'Timberland',
                'precioVenta' => 179.990,
                'publicoObjetivo' => 'hombre',
                'categoria' => 'Botas',
            ],
            [
                'codigoProducto' => 'FT007',
                'nombreProducto' => 'Comfort Sandal',
                'descripcion' => 'Sandalia ligera y cómoda para uso diario',
                'marca' => 'Adidas',
                'precioVenta' => 59.990,
                'publicoObjetivo' => 'mujer',
                'categoria' => 'Sandalias',
            ],
            [
                'codigoProducto' => 'FT008',
                'nombreProducto' => 'Kids Runner',
                'descripcion' => 'Zapatilla ligera y cómoda para niños',
                'marca' => 'Nike',
                'precioVenta' => 69.990,
                'publicoObjetivo' => 'infantil',
                'categoria' => 'Zapatillas',
            ],
        ];

        foreach ($productos as $producto) {
            $categoria = Categoria::where('nombre', $producto['categoria'])
                ->firstOrFail();

            Producto::updateOrCreate(
                [
                    'codigoProducto' => $producto['codigoProducto'],
                ],
                [
                    'nombreProducto' => $producto['nombreProducto'],
                    'descripcion' => $producto['descripcion'],
                    'marca' => $producto['marca'],
                    'precioVenta' => $producto['precioVenta'],
                    'publicoObjetivo' => $producto['publicoObjetivo'],
                    'estadoActivo' => true,
                    'id_categoria' => $categoria->id_categoria,
                ]
            );
        }
    }
}
