<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {

            $table->id('id_producto');

            $table->string('codigoProducto', 50)->unique();

            $table->string('nombreProducto', 150);

            $table->text('descripcion')->nullable();

            $table->string('marca', 100);

            $table->decimal('precioVenta', 10, 2);

            $table->boolean('estadoActivo')->default(true);

            $table->timestamp('fechaCreacion')->useCurrent();

            $table->enum('publicoObjetivo', [
                'mujer',
                'hombre',
                'unisex',
                'infantil'
            ]);

            $table->unsignedBigInteger('id_categoria');

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
