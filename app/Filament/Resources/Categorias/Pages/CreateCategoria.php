<?php

namespace App\Filament\Resources\Categorias\Pages;

use App\Filament\Resources\Categorias\CategoriaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateCategoria extends CreateRecord
{
    protected static string $resource = CategoriaResource::class;

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
