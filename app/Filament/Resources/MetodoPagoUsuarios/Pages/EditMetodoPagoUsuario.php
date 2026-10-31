<?php

namespace App\Filament\Resources\MetodoPagoUsuarios\Pages;

use App\Filament\Resources\MetodoPagoUsuarios\MetodoPagoUsuarioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMetodoPagoUsuario extends EditRecord
{
    protected static string $resource = MetodoPagoUsuarioResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // La referencia está oculta en JSON, pero el administrador puede editarla.
        $data['referenciaProveedor'] = $this->getRecord()->referenciaProveedor;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
