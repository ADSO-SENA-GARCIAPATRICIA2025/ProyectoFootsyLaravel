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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_direccion');
            $table->string('numeroOrden', 40)->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('impuestos', 12, 2)->default(0);
            $table->decimal('costoEnvio', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('estadoPedido', 30)->default('pendiente');
            $table->json('direccionEntrega');
            $table->timestamp('fechaCreacion')->useCurrent();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_direccion')->references('id_direccion')->on('direcciones')->onDelete
            //restruct es para que no se pueda eliminar un usuario o dirección si tiene pedidos asociados, evitando inconsistencias en la base de datos.
            ('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
