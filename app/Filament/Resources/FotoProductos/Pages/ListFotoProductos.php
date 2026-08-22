<?php

namespace App\Filament\Resources\FotoProductos\Pages;

use App\Filament\Resources\FotoProductos\FotoProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFotoProductos extends ListRecords
{
    protected static string $resource = FotoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
