<?php

namespace App\Filament\Resources\ItemPedidos\Pages;

use App\Filament\Resources\ItemPedidos\ItemPedidoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemPedidos extends ListRecords
{
    protected static string $resource = ItemPedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
