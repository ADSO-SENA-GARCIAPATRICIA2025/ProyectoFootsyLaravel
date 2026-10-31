<?php

namespace App\Filament\Resources\MetodoPagoUsuarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class MetodoPagoUsuarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_usuario')
                    ->label('Usuario')->relationship('usuario', 'email')
                    ->searchable()->preload()->required(),

                Select::make('id_metodo_pago')
                    ->label('Método de pago')->relationship('metodoPago', 'nombre')
                    ->searchable()->preload()->required(),

                TextInput::make('referenciaProveedor')
                    ->label('Referencia del proveedor')
                    ->helperText('Identificador entregado por el proveedor. No introduzcas números de tarjeta ni CVV.')
                    ->required()->maxLength(100)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule
                            ->where('id_usuario', $get('id_usuario'))
                            ->where('id_metodo_pago', $get('id_metodo_pago')),
                    )
                    ->validationMessages(['unique' => 'Este medio de pago ya está guardado para el usuario.']),

                TextInput::make('marca')->label('Marca')->maxLength(50),

                TextInput::make('ultimosCuatro')
                    ->label('Últimos cuatro dígitos')->length(4)
                    ->rules(['regex:/^[0-9]{4}$/']),

                Toggle::make('esPrincipal')->label('Principal')->default(false)
                    ->helperText('Al activarlo, los demás medios del usuario dejan de ser principales.'),

                Toggle::make('estadoActivo')->label('Activo')->default(true)
                    ->helperText('Un medio desactivado deja de ser principal.'),
            ]);
    }
}
