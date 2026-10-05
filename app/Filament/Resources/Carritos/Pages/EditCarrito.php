<?php

namespace App\Filament\Resources\Carritos\Pages;

use App\Filament\Resources\Carritos\CarritoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCarrito extends EditRecord
{
    protected static string $resource = CarritoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
