<?php

namespace App\Filament\Resources\FotoProductos\Pages;

use App\Filament\Resources\FotoProductos\FotoProductoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFotoProducto extends EditRecord
{
    protected static string $resource = FotoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
