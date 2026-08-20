<?php

namespace App\Filament\Resources\Productos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigoProducto')
                    ->searchable(),
                TextColumn::make('nombreProducto')
                    ->searchable(),
                TextColumn::make('marca')
                    ->searchable(),
                TextColumn::make('precioVenta')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('estadoActivo')
                    ->boolean(),
                TextColumn::make('fechaCreacion')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('publicoObjetivo')
                    ->badge(),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
            ])
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
