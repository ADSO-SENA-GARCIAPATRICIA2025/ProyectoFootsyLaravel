<?php

namespace App\Filament\Resources\ItemPedidos;

use App\Filament\Resources\ItemPedidos\Pages\CreateItemPedido;
use App\Filament\Resources\ItemPedidos\Pages\EditItemPedido;
use App\Filament\Resources\ItemPedidos\Pages\ListItemPedidos;
use App\Filament\Resources\ItemPedidos\Schemas\ItemPedidoForm;
use App\Filament\Resources\ItemPedidos\Tables\ItemPedidosTable;
use App\Models\ItemPedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemPedidoResource extends Resource
{
    protected static ?string $model = ItemPedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'itemPedido';

    public static function form(Schema $schema): Schema
    {
        return ItemPedidoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemPedidosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItemPedidos::route('/'),
            'create' => CreateItemPedido::route('/create'),
            'edit' => EditItemPedido::route('/{record}/edit'),
        ];
    }
}
