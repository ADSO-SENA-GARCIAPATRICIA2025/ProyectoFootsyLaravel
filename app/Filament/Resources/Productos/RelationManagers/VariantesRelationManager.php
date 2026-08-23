<?php

namespace App\Filament\Resources\Productos\RelationManagers;

use App\Filament\Resources\VarianteProductos\VarianteProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class VariantesRelationManager extends RelationManager
{
    protected static string $relationship = 'variantes';

    protected static ?string $relatedResource = VarianteProductoResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
