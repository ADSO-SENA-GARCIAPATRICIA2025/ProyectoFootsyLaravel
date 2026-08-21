<?php

namespace App\Filament\Resources\VarianteProductos;

use App\Filament\Resources\VarianteProductos\Pages\CreateVarianteProducto;
use App\Filament\Resources\VarianteProductos\Pages\EditVarianteProducto;
use App\Filament\Resources\VarianteProductos\Pages\ListVarianteProductos;
use App\Filament\Resources\VarianteProductos\Schemas\VarianteProductoForm;
use App\Filament\Resources\VarianteProductos\Tables\VarianteProductosTable;
use App\Models\VarianteProducto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VarianteProductoResource extends Resource
{
    protected static ?string $model = VarianteProducto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'talla';

    public static function form(Schema $schema): Schema
    {
        return VarianteProductoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VarianteProductosTable::configure($table);
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
            'index' => ListVarianteProductos::route('/'),
            'create' => CreateVarianteProducto::route('/create'),
            'edit' => EditVarianteProducto::route('/{record}/edit'),
        ];
    }
}
