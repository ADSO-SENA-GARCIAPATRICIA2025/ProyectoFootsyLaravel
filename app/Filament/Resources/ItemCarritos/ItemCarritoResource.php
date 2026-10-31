<?php

namespace App\Filament\Resources\ItemCarritos;

use App\Filament\Resources\ItemCarritos\Pages\CreateItemCarrito;
use App\Filament\Resources\ItemCarritos\Pages\EditItemCarrito;
use App\Filament\Resources\ItemCarritos\Pages\ListItemCarritos;
use App\Filament\Resources\ItemCarritos\Schemas\ItemCarritoForm;
use App\Filament\Resources\ItemCarritos\Tables\ItemCarritosTable;
use App\Models\ItemCarrito;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemCarritoResource extends Resource
{
    protected static ?string $model = ItemCarrito::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id_item_carrito';

    public static function form(Schema $schema): Schema
    {
        return ItemCarritoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemCarritosTable::configure($table);
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
            'index' => ListItemCarritos::route('/'),
            'create' => CreateItemCarrito::route('/create'),
            'edit' => EditItemCarrito::route('/{record}/edit'),
        ];
    }
}
