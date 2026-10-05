<?php

namespace App\Filament\Resources\ItemPedidos\Pages;

use App\Filament\Resources\ItemPedidos\ItemPedidoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditItemPedido extends EditRecord
{
    protected static string $resource = ItemPedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
