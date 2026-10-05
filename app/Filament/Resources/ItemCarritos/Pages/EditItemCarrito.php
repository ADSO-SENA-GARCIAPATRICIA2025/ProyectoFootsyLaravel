<?php

namespace App\Filament\Resources\ItemCarritos\Pages;

use App\Filament\Resources\ItemCarritos\ItemCarritoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditItemCarrito extends EditRecord
{
    protected static string $resource = ItemCarritoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
