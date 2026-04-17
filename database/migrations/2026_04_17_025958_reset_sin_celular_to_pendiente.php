<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('resumenes')
            ->where('estado', 'sin_celular')
            ->update(['estado' => 'pendiente', 'intentos' => 0]);
    }

    public function down(): void
    {
        // no reversible
    }
};
