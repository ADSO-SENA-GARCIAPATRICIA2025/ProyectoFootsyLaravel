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
        Schema::create('metodo_pago_usuarios', function (Blueprint $table) {
            $table->id('id_metodo_pago_usuario');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_metodo_pago');
            $table->string('referenciaProveedor', 100);
            $table->string('marca', 50)->nullable();
            $table->string('ultimosCuatro', 4)->nullable();
            $table->boolean('esPrincipal')->default(false);
            $table->boolean('estadoActivo')->default(true);
            $table->timestamp('fechaCreacion')->useCurrent();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_metodo_pago')->references('id_metodo_pago')->on('metodo_pagos')->onDelete('restrict');
            $table->unique(['id_usuario', 'id_metodo_pago', 'referenciaProveedor'], 'mpu_usuario_metodo_referencia_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodo_pago_usuarios');
    }
};
