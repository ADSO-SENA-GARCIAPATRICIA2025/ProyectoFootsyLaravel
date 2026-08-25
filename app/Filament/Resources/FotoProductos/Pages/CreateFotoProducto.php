<?php

namespace App\Filament\Resources\FotoProductos\Pages;

use App\Filament\Resources\FotoProductos\FotoProductoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateFotoProducto extends CreateRecord
{
    protected static string $resource = FotoProductoResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
