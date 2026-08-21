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
                    ->options([35 => '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'])
                    ->required(),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('estadoActivo')
                    ->required(),
                DateTimePicker::make('fechaCreacion')
                    ->required(),
                TextInput::make('id_producto')
                    ->required()
                    ->numeric(),
            ]);
    }
}
