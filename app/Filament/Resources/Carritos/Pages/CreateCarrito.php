<?php

namespace App\Filament\Resources\Carritos\Pages;

use App\Filament\Resources\Carritos\CarritoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCarrito extends CreateRecord
{
    protected static string $resource = CarritoResource::class;

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
