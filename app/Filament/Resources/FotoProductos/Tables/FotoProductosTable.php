<?php

namespace App\Filament\Resources\FotoProductos\Tables;

use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FotoProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('urlFoto')
                    ->label('Foto')
                    ->getStateUsing(function ($record): string {
                        $url = $record->urlFoto;

                        if (filter_var($url, FILTER_VALIDATE_URL)) {
                            return $url;
                        }

                        if (str_starts_with($url, 'foto-productos/')) {
                            return Storage::disk('public')->url($url);
                        }

                        return asset($url);
                    })
                    ->size(250)
                    ->square(),
                TextColumn::make('orden')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('estadoActivo')
                    ->boolean(),
                TextColumn::make('fechaCreacion')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('id_producto')
                    ->numeric()
                    ->sortable(),
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
