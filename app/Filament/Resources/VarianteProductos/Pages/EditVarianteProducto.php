<?php

namespace App\Filament\Resources\VarianteProductos\Pages;

use App\Filament\Resources\VarianteProductos\VarianteProductoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVarianteProducto extends EditRecord
{
    protected static string $resource = VarianteProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
