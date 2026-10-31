<?php

namespace App\Filament\Resources\MetodoPagoUsuarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MetodoPagoUsuariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_metodo_pago_usuario')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('usuario.email')
                    ->label('Usuario')
                    ->searchable(),

                TextColumn::make('metodoPago.nombre')
                    ->label('Método')
                    ->searchable(),

                TextColumn::make('metodoPago.proveedor')
                    ->label('Proveedor')
                    ->placeholder('Sin proveedor'),

                TextColumn::make('marca')
                    ->label('Marca')
                    ->searchable(),

                TextColumn::make('ultimosCuatro')
                    ->label('Últimos cuatro dígitos'),

                IconColumn::make('esPrincipal')
                    ->label('Principal')
                    ->boolean(),

                IconColumn::make('estadoActivo')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('fechaCreacion')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id_metodo_pago_usuario', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
