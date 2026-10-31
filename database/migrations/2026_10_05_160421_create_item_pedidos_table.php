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
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->id('id_item_pedido');
            $table->unsignedBigInteger('id_pedido');
            $table->unsignedBigInteger('id_variante');
            $table->unsignedInteger('cantidad');
            $table->decimal('precioUnitario', 10, 2);
            $table->decimal('subtotalLinea', 12, 2);

            $table->foreign('id_pedido')
                ->references('id_pedido')
                ->on('pedidos')
                ->onDelete('cascade');

            $table->foreign('id_variante')
                ->references('id_variante')
                ->on('variante_productos')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pedidos');
    }
};
