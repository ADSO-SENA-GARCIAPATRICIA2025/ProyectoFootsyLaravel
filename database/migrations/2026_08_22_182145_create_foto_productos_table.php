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
        Schema::create('foto_productos', function (Blueprint $table) {
            $table->id('id_foto');
            $table->string('urlFoto');
            $table ->integer('order')->default(1);
            $table->boolean('estadoActivo')->default(true);
            $table->timestamp('fechaCreacion')->useCurrent();

            $table->unsignedBigInteger('id_producto');
            
            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('productos')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_productos');
    }
};
