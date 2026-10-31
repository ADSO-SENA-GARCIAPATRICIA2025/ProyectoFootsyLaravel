<?php

namespace App\Filament\Resources\ItemPedidos\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemPedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_item_pedido')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('pedido.numeroOrden')
                    ->label('Pedido')
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

                TextColumn::make('precioUnitario')
                    ->label('Precio unitario')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('subtotalLinea')
                    ->label('Subtotal de la línea')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->defaultSort('id_item_pedido', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
