<?php

namespace App\Filament\Resources\ItemCarritos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemCarritosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_item_carrito')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('id_carrito')
                    ->label('Carrito')
                    ->sortable(),

                TextColumn::make('carrito.usuario.email')
                    ->label('Usuario')
                    ->searchable(),

                TextColumn::make('variante.producto.nombreProducto')
                    ->label('Producto')
                    ->searchable(),

                TextColumn::make('variante.talla')
                    ->label('Talla'),

                TextColumn::make('variante.color')
                    ->label('Color')
                    ->searchable(),

                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable(),

                TextColumn::make('fechaCreacion')
                    ->label('Fecha de creacion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id_item_carrito', 'desc')
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
