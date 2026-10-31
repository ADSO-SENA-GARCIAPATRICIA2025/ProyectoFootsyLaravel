<?php

namespace App\Filament\Resources\MetodoPagoUsuarios\Pages;

use App\Filament\Resources\MetodoPagoUsuarios\MetodoPagoUsuarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMetodoPagoUsuarios extends ListRecords
{
    protected static string $resource = MetodoPagoUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
