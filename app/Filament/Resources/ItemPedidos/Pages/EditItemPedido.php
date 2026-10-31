<?php

namespace App\Filament\Resources\ItemPedidos\Pages;

use App\Filament\Resources\ItemPedidos\ItemPedidoResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Models\ItemPedido;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditItemPedido extends EditRecord
{
    protected static string $resource = ItemPedidoResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (ValidationException $exception) {
            throw ItemPedidoResource::erroresDelFormulario($exception);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->using(function (ItemPedido $record, DeleteAction $action) {
                    try {
                        return $record->delete();
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->title('No se pudo eliminar la línea')
                            ->body(collect($exception->errors())->flatten()->implode(' '))
                            ->danger()
                            ->send();
                        $action->halt();
                    }
                }),
        ];
    }
}
