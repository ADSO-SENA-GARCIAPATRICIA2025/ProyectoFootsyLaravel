<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('apellido')
                    ->label('Apellido')
                    ->required(),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                     ->autocomplete(false)
                    ->required(),

                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel(),

                DatePicker::make('fechaNacimiento')
                    ->label('Fecha de nacimiento'),

                Select::make('rolUsuario')
                    ->label('Rol')
                    ->options([
                        'cliente' => 'Cliente',
                        'admin' => 'Administrador',
                    ])
                    ->default('cliente')
                    ->required(),

                Select::make('genero')
                    ->label('Género')
                    ->options([
                        'mujer' => 'Mujer',
                        'hombre' => 'Hombre',
                        'otro' => 'Otro',
                        'prefiero_no_decirlo' => 'Prefiero no decirlo',
                    ]),

                Toggle::make('estadoActivo')
                    ->label('Usuario activo')
                    ->default(true),

              TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->autocomplete('new-password')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state)),
            ]);
    }
}
