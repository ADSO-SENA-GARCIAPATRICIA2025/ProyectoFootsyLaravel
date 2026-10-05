<?php

namespace App\Filament\Resources\Carritos;

use App\Filament\Resources\Carritos\Pages\CreateCarrito;
use App\Filament\Resources\Carritos\Pages\EditCarrito;
use App\Filament\Resources\Carritos\Pages\ListCarritos;
use App\Filament\Resources\Carritos\Schemas\CarritoForm;
use App\Filament\Resources\Carritos\Tables\CarritosTable;
use App\Models\Carrito;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarritoResource extends Resource
{
    protected static ?string $model = Carrito::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'carrito';

    public static function form(Schema $schema): Schema
    {
        return CarritoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarritosTable::configure($table);
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
            'index' => ListCarritos::route('/'),
            'create' => CreateCarrito::route('/create'),
            'edit' => EditCarrito::route('/{record}/edit'),
        ];
    }
}
