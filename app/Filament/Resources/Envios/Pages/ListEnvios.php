<?php

namespace App\Filament\Resources\Envios\Pages;

use App\Filament\Resources\Envios\EnvioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnvios extends ListRecords
{
    protected static string $resource = EnvioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
