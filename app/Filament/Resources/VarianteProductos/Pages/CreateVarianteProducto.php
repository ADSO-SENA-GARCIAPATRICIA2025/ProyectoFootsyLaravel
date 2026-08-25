<?php

namespace App\Filament\Resources\VarianteProductos\Pages;

use App\Filament\Resources\VarianteProductos\VarianteProductoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
class CreateVarianteProducto extends CreateRecord
{
    protected static string $resource = VarianteProductoResource::class;

            protected function getFormActions(): array
{
    return [
        $this->getCreateFormAction(),
        $this->getCreateAnotherFormAction(),

        Action::make('back')
            ->label('Atrás')
            ->url($this->getResource()::getUrl('index'))
            ->color('gray'),
    ];
}
}
