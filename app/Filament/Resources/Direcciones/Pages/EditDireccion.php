<?php

namespace App\Filament\Resources\Direcciones\Pages;

use App\Filament\Resources\Direcciones\DireccionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDireccion extends EditRecord
{
    protected static string $resource = DireccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
