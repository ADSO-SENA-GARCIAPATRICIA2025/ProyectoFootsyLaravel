<?php

namespace App\Filament\Resources\Productos\RelationManagers;

use App\Filament\Resources\FotoProductos\FotoProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class FotosRelationManager extends RelationManager
{
    protected static string $relationship = 'fotos';

    protected static ?string $relatedResource = FotoProductoResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
