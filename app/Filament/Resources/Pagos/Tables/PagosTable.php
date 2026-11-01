<?php

namespace App\Filament\Resources\Pagos\Tables;

use App\Models\Pago;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PagosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_pago')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('pedido.numeroOrden')
                    ->label('Pedido')
                    ->searchable(),

                TextColumn::make('metodoPago.nombre')
                    ->label('Método de pago')
                    ->searchable(),

                TextColumn::make('referenciaTransaccion')
                    ->label('Referencia de la transacción')
                    ->placeholder('Sin referencia')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('estadoPago')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => Pago::ESTADOS[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aprobado' => 'success',
                        'rechazado' => 'danger',
                        'pendiente' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('fechaCreacion')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('fechaPago')
                    ->label('Fecha de pago')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin fecha de aprobación')
                    ->sortable(),
            ])
            ->defaultSort('id_pago', 'desc')
            ->filters([
                SelectFilter::make('estadoPago')
                    ->label('Estado del pago')
                    ->options(Pago::ESTADOS),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
