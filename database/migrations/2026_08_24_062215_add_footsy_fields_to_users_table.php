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
        Schema::table('users', function (Blueprint $table) {

                    $table->string('apellido', 100)->after('name');

                    $table->string('telefono', 30)->nullable()->after('email');

                    $table->date('fechaNacimiento')->nullable()->after('telefono');

                    $table->enum('rolUsuario', [
                        'cliente',
                        'admin',
                    ])->default('cliente')->after('fechaNacimiento');

                    $table->enum('genero', [
                        'mujer',
                        'hombre',
                        'otro',
                        'prefiero_no_decirlo',
                    ])->nullable()->after('rolUsuario');

                    $table->boolean('estadoActivo')
                        ->default(true)
                        ->after('genero');
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
                $table->dropColumn([
                    'apellido',
                    'telefono',
                    'fechaNacimiento',
                    'rolUsuario',
                    'genero',
                    'estadoActivo',
                ]);
            });
    }
};
