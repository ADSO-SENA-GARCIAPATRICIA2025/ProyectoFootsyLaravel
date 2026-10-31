<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_carritos', function (Blueprint $table) {
            $table->id('id_item_carrito');
            $table->unsignedInteger('cantidad');
            $table->timestamp('fechaCreacion')->useCurrent();

            $table->unsignedBigInteger('id_carrito');
            $table->unsignedBigInteger('id_variante');

            $table->foreign('id_carrito')
                ->references('id_carrito')
                ->on('carritos')
                ->onDelete('cascade');

            $table->foreign('id_variante')
                ->references('id_variante')
                ->on('variante_productos')
                ->onDelete('restrict');

            $table->unique(['id_carrito', 'id_variante']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_carritos');
    }
};
