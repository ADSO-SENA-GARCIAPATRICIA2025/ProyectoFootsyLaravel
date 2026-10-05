<?php

namespace App\Filament\Resources\Direcciones;

use App\Filament\Resources\Direcciones\Pages\CreateDireccion;
use App\Filament\Resources\Direcciones\Pages\EditDireccion;
use App\Filament\Resources\Direcciones\Pages\ListDirecciones;
use App\Filament\Resources\Direcciones\Schemas\DireccionForm;
use App\Filament\Resources\Direcciones\Tables\DireccionesTable;
use App\Models\Direccion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DireccionResource extends Resource
{
    protected static ?string $model = Direccion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Direcciones';

    protected static ?string $modelLabel = 'dirección';

    protected static ?string $pluralModelLabel = 'direcciones';

    protected static ?string $recordTitleAttribute = 'direccion';

    public static function form(Schema $schema): Schema
    {
        return DireccionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DireccionesTable::configure($table);
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
            'index' => ListDirecciones::route('/'),
            'create' => CreateDireccion::route('/create'),
            'edit' => EditDireccion::route('/{record}/edit'),
        ];
    }
}
