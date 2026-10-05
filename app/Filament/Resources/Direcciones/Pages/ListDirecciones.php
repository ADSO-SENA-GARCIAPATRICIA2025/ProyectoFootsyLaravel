<?php

namespace App\Filament\Resources\Direcciones\Pages;

use App\Filament\Resources\Direcciones\DireccionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDirecciones extends ListRecords
{
    protected static string $resource = DireccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
