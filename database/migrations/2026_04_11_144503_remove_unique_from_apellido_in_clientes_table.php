<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El constraint UNIQUE en apellido impedía registrar dos socios con el mismo apellido.
     * Ahora que nro_socio es la clave única real (BUG 5), apellido puede repetirse.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique('clientes_apellido_unique');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unique('apellido');
        });
    }
};