<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('periodo', 7); // formato Y-m, ej: "2026-03"
            $table->string('pdf_path')->nullable();
            $table->enum('estado', ['pendiente', 'enviando', 'notificado', 'error', 'sin_celular'])
                  ->default('pendiente');
            $table->timestamp('enviado_at')->nullable();
            $table->unsignedTinyInteger('intentos')->default(0);
            $table->timestamps();

            $table->unique(['cliente_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumenes');
    }
};
