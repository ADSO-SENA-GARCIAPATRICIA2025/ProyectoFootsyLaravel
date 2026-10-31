<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use App\Models\Pedido;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PedidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numeroOrden')
                    ->label('Número de orden')
                    ->placeholder('Se genera al guardar')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('id_usuario')
                    ->label('Usuario')
                    ->relationship('usuario', 'email')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('id_direccion', null))
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                Select::make('id_direccion')
                    ->label('Dirección de entrega')
                    ->relationship('direccion', 'direccion', fn (Builder $query, Get $get, string $operation) => $query
                        ->where('id_usuario', $get('id_usuario'))
                        ->when($operation !== 'edit', fn (Builder $query) => $query->where('estadoActivo', true)))
                    ->searchable()
                    ->preload()
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                Select::make('estadoPedido')
                    ->label('Estado del pedido')
                    ->options(fn (?Pedido $record): array => $record === null
                        ? ['pendiente' => Pedido::ESTADOS['pendiente']]
                        : array_intersect_key(Pedido::ESTADOS, array_flip([
                            $record->estadoPedido, ...Pedido::TRANSICIONES[$record->estadoPedido],
                        ])))
                    ->default('pendiente')
                    ->required(),

                TextInput::make('subtotal')
                    ->label('Subtotal antes de impuestos')
                    ->helperText('Cuando hay líneas, el subtotal se obtiene automáticamente del detalle.')
                    ->numeric()
                    ->rules(Pedido::reglasImporte())
                    ->disabled(fn (?Pedido $record): bool => $record !== null && ($record->estadoPedido !== 'pendiente' || $record->items()->exists()))
                    ->required(),

                TextInput::make('descuento')
                    ->label('Descuento')
                    ->numeric()
                    ->rules(Pedido::reglasImporte())
                    ->default(0)
                    ->disabled(fn (?Pedido $record): bool => $record !== null && $record->estadoPedido !== 'pendiente')
                    ->required(),

                TextInput::make('impuestos')
                    ->label('Impuestos')
                    ->numeric()
                    ->rules(Pedido::reglasImporte())
                    ->default(0)
                    ->disabled(fn (?Pedido $record): bool => $record !== null && $record->estadoPedido !== 'pendiente')
                    ->required(),

                TextInput::make('costoEnvio')
                    ->label('Costo de envío')
                    ->numeric()
                    ->rules(Pedido::reglasImporte())
                    ->default(0)
                    ->disabled(fn (?Pedido $record): bool => $record !== null && $record->estadoPedido !== 'pendiente')
                    ->required(),

                TextInput::make('total')
                    ->label('Total calculado')
                    ->placeholder('Se calcula al guardar')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
