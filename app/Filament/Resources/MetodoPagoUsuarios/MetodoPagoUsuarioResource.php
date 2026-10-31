<?php

namespace App\Filament\Resources\MetodoPagoUsuarios;

use App\Filament\Resources\MetodoPagoUsuarios\Pages\CreateMetodoPagoUsuario;
use App\Filament\Resources\MetodoPagoUsuarios\Pages\EditMetodoPagoUsuario;
use App\Filament\Resources\MetodoPagoUsuarios\Pages\ListMetodoPagoUsuarios;
use App\Filament\Resources\MetodoPagoUsuarios\Schemas\MetodoPagoUsuarioForm;
use App\Filament\Resources\MetodoPagoUsuarios\Tables\MetodoPagoUsuariosTable;
use App\Models\MetodoPagoUsuario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MetodoPagoUsuarioResource extends Resource
{
    protected static ?string $model = MetodoPagoUsuario::class;

    protected static ?string $recordTitleAttribute = 'id_metodo_pago_usuario';

    protected static ?string $modelLabel = 'medio de pago guardado';

    protected static ?string $pluralModelLabel = 'medios de pago guardados';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MetodoPagoUsuarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MetodoPagoUsuariosTable::configure($table);
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
            'index' => ListMetodoPagoUsuarios::route('/'),
            'create' => CreateMetodoPagoUsuario::route('/create'),
            'edit' => EditMetodoPagoUsuario::route('/{record}/edit'),
        ];
    }
}
