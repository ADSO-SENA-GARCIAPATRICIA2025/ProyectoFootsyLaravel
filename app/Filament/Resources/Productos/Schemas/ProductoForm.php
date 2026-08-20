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
                    ->required(),

                TextInput::make('nombreProducto')
                    ->required(),

                Textarea::make('descripcion')
                    ->columnSpanFull(),

                TextInput::make('marca')
                    ->required(),

                TextInput::make('precioVenta')
                    ->required()
                    ->numeric(),

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
