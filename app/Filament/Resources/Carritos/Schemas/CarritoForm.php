<?php

namespace App\Filament\Resources\Carritos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CarritoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_usuario')
                    ->label('Usuario')
                    ->relationship('usuario', 'email')
                    ->searchable()
                    ->preload()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Este usuario ya tiene un carrito. Edita el carrito existente.',
                    ])
                    ->required(),

                Toggle::make('estadoActivo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
