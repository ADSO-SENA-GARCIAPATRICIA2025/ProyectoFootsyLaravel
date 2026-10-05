<?php

namespace App\Filament\Resources\Direcciones\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DireccionForm
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
                    ->required(),

                TextInput::make('nombreDestinatario')
                    ->label('Nombre destinatario')
                    ->required()
                    ->maxLength(150),

                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->maxLength(30),

                TextInput::make('direccion')
                    ->label('Dirección')
                    ->required()
                    ->maxLength(255),

                TextInput::make('ciudad')
                    ->required()
                    ->maxLength(100),

                TextInput::make('departamento')
                    ->maxLength(100),

                TextInput::make('codigoPostal')
                    ->label('Código postal')
                    ->maxLength(20),

                TextInput::make('pais')
                    ->label('País')
                    ->required()
                    ->maxLength(100),

                Textarea::make('indicaciones')
                    ->label('Indicaciones'),

                Toggle::make('esPrincipal')
                    ->label('Dirección principal')
                    ->default(false),

                Toggle::make('estadoActivo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}

