<?php

namespace App\Filament\Resources\ItemPedidos\Schemas;

use App\Models\VarianteProducto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ItemPedidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pedido')
                    ->label('Pedido')
                    ->relationship('pedido', 'numeroOrden', fn (Builder $query) => $query->where('estadoPedido', 'pendiente'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                Select::make('id_variante')
                    ->label('Variante del producto')
                    ->relationship('variante', 'id_variante', fn (Builder $query) => $query->with('producto'))
                    ->getOptionLabelFromRecordUsing(fn (VarianteProducto $record): string =>
                        $record->producto?->nombreProducto . ' - Talla ' . $record->talla . ' - ' . $record->color
                    )
                    ->searchable(['color', 'talla'])
                    ->preload()
                    ->live()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('id_pedido', $get('id_pedido')),
                    )
                    ->validationMessages(['unique' => 'Esta variante ya está en el pedido. Edita su cantidad.'])
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->required(),

                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(fn (Get $get): int => VarianteProducto::find($get('id_variante'))?->stock ?? 0)
                    ->validationMessages(['max' => 'La cantidad supera el stock disponible de la variante.'])
                    ->default(1)
                    ->required(),

                TextInput::make('precioUnitario')
                    ->label('Precio unitario de la compra')
                    ->placeholder('Se toma del producto al guardar')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('subtotalLinea')
                    ->label('Subtotal de la línea')
                    ->placeholder('Se calcula al guardar')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
