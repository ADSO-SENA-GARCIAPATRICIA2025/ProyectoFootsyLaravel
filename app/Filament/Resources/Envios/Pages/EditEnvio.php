<?php

namespace App\Filament\Resources\Envios\Pages;

use App\Filament\Resources\Envios\EnvioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEnvio extends EditRecord
{
    protected static string $resource = EnvioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
