<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('codigoProducto')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->required(),

                TextInput::make('nombreProducto')
                    ->maxLength(150)
                    ->required(),

                Textarea::make('descripcion')
                    ->columnSpanFull(),

                TextInput::make('marca')
                    ->maxLength(100)
                    ->required(),

                TextInput::make('precioVenta')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999.99)
                    ->rules(['decimal:0,2']),

                Toggle::make('estadoActivo')
                    ->default(true),

                Select::make('publicoObjetivo')
                    ->options([
                        'mujer' => 'Mujer',
                        'hombre' => 'Hombre',
                        'unisex' => 'Unisex',
                        'infantil' => 'Infantil',
                    ])
                    ->required(),

                Select::make('id_categoria')
                    ->relationship('categoria', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
