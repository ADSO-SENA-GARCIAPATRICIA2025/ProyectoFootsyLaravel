<?php

namespace App\Filament\Resources\FotoProductos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FotoProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                FileUpload::make('urlFoto')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('foto-productos')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->required(),

                TextInput::make('orden')
                    ->required()
                    ->numeric()
                    ->default(1),

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
