<?php

namespace App\Filament\Resources\ItemPedidos\Pages;

use App\Filament\Resources\ItemPedidos\ItemPedidoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateItemPedido extends CreateRecord
{
    protected static string $resource = ItemPedidoResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (ValidationException $exception) {
            throw ItemPedidoResource::erroresDelFormulario($exception);
        }
    }
}
