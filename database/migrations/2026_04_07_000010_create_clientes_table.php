<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('apellido')->unique();
            $table->string('nombre_completo');
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('nro_socio')->nullable();
            $table->string('ultimo_periodo', 7)->nullable(); // formato Y-m
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
