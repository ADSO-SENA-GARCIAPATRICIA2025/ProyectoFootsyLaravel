<?php

namespace App\Filament\Resources\MetodoPagos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MetodoPagosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_metodo_pago')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->placeholder('Sin proveedor')
                    ->searchable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->wrap(),

                IconColumn::make('estadoActivo')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('fechaCreacion')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id_metodo_pago', 'desc')
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
