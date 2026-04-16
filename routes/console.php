<?php

use App\Models\Resumen;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Resetea resúmenes atascados en "enviando" → "pendiente"
// Correr tras reinicio del queue worker si se cortó mid-envío
Artisan::command('resumenes:reset-enviando', function () {
    $count = Resumen::where('estado', 'enviando')->update(['estado' => 'pendiente']);
    $this->info("Reseteados {$count} resúmenes de 'enviando' → 'pendiente'.");
})->purpose('Resetear resúmenes atascados en estado enviando');
