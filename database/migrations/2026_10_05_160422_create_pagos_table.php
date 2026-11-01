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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_pedido');
            $table->unsignedBigInteger('id_metodo_pago');
            $table->string('referenciaTransaccion', 255)->nullable();
            $table->decimal('monto', 12, 2);
            // El estado del pago puede ser 'pendiente', 'completado', 'fallido', etc. fue definido en el modelo de Pago
            $table->string('estadoPago', 20)->default('pendiente');

            $table->timestamp('fechaCreacion')->useCurrent();
            // es nullable() para permitir que la fecha de pago sea nula si el pago aún no se ha realizado
            $table->timestamp('fechaPago')->nullable();
            $table->unique(['id_metodo_pago', 'referenciaTransaccion'], 'pagos_metodo_referencia_unique');

            $table->foreign('id_pedido')
                ->references('id_pedido')
                ->on('pedidos')
                ->onDelete('restrict');

            $table->foreign('id_metodo_pago')
                ->references('id_metodo_pago')
                ->on('metodo_pagos')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
