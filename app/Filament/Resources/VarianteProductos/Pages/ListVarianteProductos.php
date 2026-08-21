<?php

namespace App\Filament\Resources\VarianteProductos\Pages;

use App\Filament\Resources\VarianteProductos\VarianteProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVarianteProductos extends ListRecords
{
    protected static string $resource = VarianteProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
