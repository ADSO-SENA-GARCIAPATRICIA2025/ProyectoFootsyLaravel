<?php

namespace App\Filament\Resources\VarianteProductos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VarianteProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('color')
                    ->required(),
                Select::make('talla')
                    ->options([
                          '35' => '35',
                        '36' => '36',
                        '37' => '37',
                        '38' => '38',
                        '39' => '39',
                        '40' => '40',
                        '41' => '41',
                        '42' => '42',
                        '43' => '43',
                        '44' => '44',
                        '45' => '45',])
                    ->required(),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('estadoActivo')
                    ->default(true),
                Select::make('id_producto')
                       ->relationship('producto', 'nombreProducto')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
