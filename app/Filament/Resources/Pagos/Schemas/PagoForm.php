<?php

namespace App\Filament\Resources\Pagos\Schemas;

use App\Models\Pago;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PagoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pedido')
                    ->label('Pedido')
                    ->relationship('pedido', 'numeroOrden')
                    ->searchable()
                    ->preload()
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                Select::make('id_metodo_pago')
                    ->label('Método de pago')
                    ->relationship('metodoPago', 'nombre')
                    ->searchable()
                    ->preload()
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                TextInput::make('referenciaTransaccion')
                    ->label('Referencia de la transacción')
                    ->maxLength(255),

                TextInput::make('monto')
                    ->label('Monto')
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(9999999999.99)
                    ->rules(['decimal:0,2'])
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                Select::make('estadoPago')
                    ->label('Estado del pago')
                    ->options(fn (?Pago $record): array => $record === null
                        ? ['pendiente' => Pago::ESTADOS['pendiente']]
                        : array_intersect_key(Pago::ESTADOS, array_flip([
                            $record->estadoPago, ...Pago::TRANSICIONES[$record->estadoPago],
                        ])))
                    ->default('pendiente')
                    ->required(),
            ]);
    }
}
