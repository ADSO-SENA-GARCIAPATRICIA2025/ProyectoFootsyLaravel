<?php

namespace App\Filament\Resources\Productos\Pages;

use App\Filament\Resources\Productos\ProductoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

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
