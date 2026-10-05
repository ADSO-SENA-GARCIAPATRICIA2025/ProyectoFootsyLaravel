<?php

namespace App\Filament\Resources\ItemCarritos\Pages;

use App\Filament\Resources\ItemCarritos\ItemCarritoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemCarritos extends ListRecords
{
    protected static string $resource = ItemCarritoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
