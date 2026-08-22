<?php

namespace App\Filament\Resources\FotoProductos;

use App\Filament\Resources\FotoProductos\Pages\CreateFotoProducto;
use App\Filament\Resources\FotoProductos\Pages\EditFotoProducto;
use App\Filament\Resources\FotoProductos\Pages\ListFotoProductos;
use App\Filament\Resources\FotoProductos\Schemas\FotoProductoForm;
use App\Filament\Resources\FotoProductos\Tables\FotoProductosTable;
use App\Models\FotoProducto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FotoProductoResource extends Resource
{
    protected static ?string $model = FotoProducto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'urlFoto';

    public static function form(Schema $schema): Schema
    {
        return FotoProductoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FotoProductosTable::configure($table);
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
            'index' => ListFotoProductos::route('/'),
            'create' => CreateFotoProducto::route('/create'),
            'edit' => EditFotoProducto::route('/{record}/edit'),
        ];
    }
}
