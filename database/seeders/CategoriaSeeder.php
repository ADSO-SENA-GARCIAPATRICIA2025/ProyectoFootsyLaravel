<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Running',
                'descripcion' => 'Calzado deportivo diseñado para correr',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Fútbol',
                'descripcion' => 'Calzado diseñado para la práctica del fútbol',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Training',
                'descripcion' => 'Calzado deportivo para entrenamiento y gimnasio',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Casual',
                'descripcion' => 'Calzado para uso diario y actividades informales',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Deportivo',
                'descripcion' => 'Calzado deportivo para diferentes actividades',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Botas',
                'descripcion' => 'Calzado de caña alta o media para diferentes usos',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Sandalias',
                'descripcion' => 'Calzado abierto para uso diario y temporadas cálidas',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Zapatillas',
                'descripcion' => 'Calzado cómodo de uso cotidiano',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Tacones',
                'descripcion' => 'Calzado femenino con tacón',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Mocasines',
                'descripcion' => 'Calzado cerrado y cómodo de estilo clásico o casual',
                'estadoActivo' => true,
            ],
            [
                'nombre' => 'Escolar',
                'descripcion' => 'Calzado destinado al uso escolar infantil',
                'estadoActivo' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(
                ['nombre' => $categoria['nombre']],
                $categoria
            );
        }
    }
}
