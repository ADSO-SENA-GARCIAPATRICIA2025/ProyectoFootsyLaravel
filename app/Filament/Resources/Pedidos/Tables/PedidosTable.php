<?php

namespace App\Filament\Resources\Pedidos\Tables;

use Filament\Actions\EditAction;
use App\Models\Pedido;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numeroOrden')
                    ->label('Número de orden')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('usuario.email')
                    ->label('Usuario')
                    ->searchable(),

                TextColumn::make('estadoPedido')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => Pedido::ESTADOS[$state] ?? $state)
                    ->badge(),

                TextColumn::make('subtotal')->label('Subtotal')->numeric(decimalPlaces: 2),
                TextColumn::make('descuento')->label('Descuento')->numeric(decimalPlaces: 2),
                TextColumn::make('impuestos')->label('Impuestos')->numeric(decimalPlaces: 2),
                TextColumn::make('costoEnvio')->label('Envío')->numeric(decimalPlaces: 2),

                TextColumn::make('total')
                    ->label('Total')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('direccionEntrega.direccion')
                    ->label('Dirección de la compra')
                    ->wrap(),

                TextColumn::make('fechaCreacion')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id_pedido', 'desc')
            ->filters([
                SelectFilter::make('estadoPedido')
                    ->label('Estado')
                    ->options(Pedido::ESTADOS),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
