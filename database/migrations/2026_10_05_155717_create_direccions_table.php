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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id('id_direccion');
            $table->string('nombreDestinatario', 150);
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 255);
            $table->string('ciudad', 100);
            $table->string('departamento', 100)->nullable();
            $table->string('codigoPostal', 20)->nullable();
            $table->string('pais', 100);
            $table->text('indicaciones')->nullable();
            $table->boolean('esPrincipal')->default(false);
            $table->boolean('estadoActivo')->default(true);
            $table->timestamp('fechaCreacion')->useCurrent();

            $table->unsignedBigInteger('id_usuario');

            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
