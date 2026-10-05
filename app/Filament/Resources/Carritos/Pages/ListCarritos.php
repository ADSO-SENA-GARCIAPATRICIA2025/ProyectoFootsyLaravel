<?php

namespace App\Filament\Resources\Carritos\Pages;

use App\Filament\Resources\Carritos\CarritoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarritos extends ListRecords
{
    protected static string $resource = CarritoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
