<?php

namespace App\Filament\Resources\Carritos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarritosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_carrito'),

                TextColumn::make('usuario.email')
                    ->label('Usuario')
                    ->searchable(),

                IconColumn::make('estadoActivo')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('fechaCreacion')
                    ->dateTime(),
            ])
            ->defaultSort('id_carrito', 'desc')
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
