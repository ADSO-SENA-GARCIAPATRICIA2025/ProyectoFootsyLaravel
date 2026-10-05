<?php

namespace App\Filament\Resources\Direcciones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DireccionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('usuario.email')
                    ->label('Usuario'),

                TextColumn::make('nombreDestinatario')
                    ->label('Destinatario')
                    ->searchable(),

                TextColumn::make('direccion')
                    ->searchable(),

                TextColumn::make('ciudad')
                    ->searchable(),

                TextColumn::make('pais'),

                IconColumn::make('esPrincipal')
                    ->label('Principal')
                    ->boolean(),

                IconColumn::make('estadoActivo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
