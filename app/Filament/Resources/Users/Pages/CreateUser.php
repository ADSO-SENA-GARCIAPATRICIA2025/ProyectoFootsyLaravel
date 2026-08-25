<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

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
