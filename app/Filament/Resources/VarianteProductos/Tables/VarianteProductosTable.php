<?php

namespace App\Filament\Resources\VarianteProductos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VarianteProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('color')
                    ->searchable(),
                TextColumn::make('talla')
                    ->badge(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('estadoActivo')
                    ->boolean(),
                TextColumn::make('fechaCreacion')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('producto.nombreProducto')
                    ->label('Producto')
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
