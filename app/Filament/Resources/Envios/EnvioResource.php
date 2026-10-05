<?php

namespace App\Filament\Resources\Envios;

use App\Filament\Resources\Envios\Pages\CreateEnvio;
use App\Filament\Resources\Envios\Pages\EditEnvio;
use App\Filament\Resources\Envios\Pages\ListEnvios;
use App\Filament\Resources\Envios\Schemas\EnvioForm;
use App\Filament\Resources\Envios\Tables\EnviosTable;
use App\Models\Envio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EnvioResource extends Resource
{
    protected static ?string $model = Envio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'envio';

    public static function form(Schema $schema): Schema
    {
        return EnvioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnviosTable::configure($table);
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
            'index' => ListEnvios::route('/'),
            'create' => CreateEnvio::route('/create'),
            'edit' => EditEnvio::route('/{record}/edit'),
        ];
    }
}
