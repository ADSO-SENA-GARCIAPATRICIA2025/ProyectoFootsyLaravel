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
        Schema::create('variante_productos', function (Blueprint $table) {
    $table->id('id_variante');

    $table->string('color', 50);

    $table->enum('talla', [
        '35',
        '36',
        '37',
        '38',
        '39',
        '40',
        '41',
        '42',
        '43',
        '44',
        '45',
    ]);

    $table->integer('stock')->default(0);

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
        Schema::dropIfExists('variante_productos');
    }
};
