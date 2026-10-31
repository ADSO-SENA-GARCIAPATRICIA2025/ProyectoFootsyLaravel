<?php

namespace App\Filament\Resources\ItemCarritos\Schemas;

use App\Models\Carrito;
use App\Models\VarianteProducto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ItemCarritoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_carrito')
                    ->label('Carrito')
                    //relationship() utiliza el nombre de la relación del modelo: carrito o variante.
                    ->relationship('carrito', 'id_carrito', fn (Builder $query) => $query->with('usuario'))
                    //define el texto visible: carrito con correo del usuario, o producto con talla y color. Al guardar, se almacena el ID.
                    //carga los datos del usuario o producto para construir esas etiquetas
                    ->getOptionLabelFromRecordUsing(fn (Carrito $record): string =>
                        'Carrito #' . $record->id_carrito . ' - ' . $record->usuario?->email
                    )

                    //permite buscar por número de carrito, o por color y talla.
                    ->searchable(['id_carrito'])
                    //carga las opciones al abrir el formulario.
                    ->preload()
                    //marca el campo como obligatorio.
                    ->required(),

                Select::make('id_variante')
                    ->label('Variante del producto')
                    ->relationship('variante', 'id_variante', fn (Builder $query) => $query->with('producto'))
                    ->getOptionLabelFromRecordUsing(fn (VarianteProducto $record): string =>
                        $record->producto?->nombreProducto . ' - Talla ' . $record->talla . ' - ' . $record->color
                    )
                    //permite buscar por color y talla.
                    ->searchable(['color', 'talla'])
                    //carga las opciones al abrir el formulario.
                    ->preload()
                    //marca el campo como obligatorio.
                    ->live()
                    //valida que la variante seleccionada sea única dentro del mismo carrito
                    ->unique(
                        //define la tabla y columna para la validación de unicidad
                        table: 'item_carritos',
                        //valida que la variante seleccionada sea única dentro del mismo carrito
                        column: 'id_variante',
                        //ignora el registro actual al validar la unicidad, para permitir actualizar la cantidad del mismo item
                        ignoreRecord: true,
                        //modifica la regla de validación para que solo sea única dentro del mismo carrito
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique =>
                        //agrega una condición where para que la validación de unicidad solo se aplique dentro del mismo carrito
                            $rule->where('id_carrito',
                            //obtiene el valor del campo id_carrito del formulario
                            $get('id_carrito')),
                    )
                    ->validationMessages([
                        'unique' => 'Esta variante ya existe en el carrito. Edita la cantidad del item existente.',
                    ])
                    ->required(),

                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->integer()
                    //establece un valor mínimo de 1
                    ->minValue(1)
                    //establece un valor máximo igual al stock disponible de la variante seleccionada
                    ->maxValue(fn (Get $get): int =>
                        VarianteProducto::find($get('id_variante'))?->stock ?? 0
                    )
                    ->validationMessages([
                        'max' => 'La cantidad supera el stock disponible de la variante seleccionada.',
                    ])
                    //establece un valor por defecto de 1
                    ->default(1)
                    ->required(),
            ]);
    }
}
