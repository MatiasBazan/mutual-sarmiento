<?php

use App\Http\Controllers\AdminBoxController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumenController;
use App\Http\Controllers\TurneroController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

// Redirigir raíz según rol
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isTurnero()) {
            return redirect()->route('turnero.panel');
        }
        return redirect()->route('resumenes.index');
    }
    return redirect()->route('login');
});

// Pantalla TV — pública, sin login
Route::get('/tv', [TurneroController::class, 'tv'])->name('turnero.tv');

// Webhook de Meta WhatsApp — público, sin CSRF (Meta no envía token)
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp.handle');

// Rutas autenticadas
Route::middleware(['auth'])->group(function () {

    // Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Turnero — todos los usuarios autenticados
    Route::get('/turnero', [TurneroController::class, 'panel'])->name('turnero.panel');
    Route::patch('/turnero/status', [TurneroController::class, 'updateStatus'])->name('turnero.status');

    // Resúmenes — admin y empleados (no turnero)
    Route::middleware(['no-turnero'])->group(function () {
        Route::get('/resumenes', [ResumenController::class, 'index'])->name('resumenes.index');
        Route::get('/resumenes/importar', fn() => view('resumenes.importar'))->name('resumenes.importar');
        Route::post('/resumenes/importar', [ResumenController::class, 'importar'])->middleware('throttle:10,1')->name('resumenes.importar.store');
        Route::post('/resumenes/enviar-todos', [ResumenController::class, 'enviarTodos'])->middleware('throttle:5,1')->name('resumenes.enviar-todos');
        Route::post('/resumenes/{resumen}/enviar', [ResumenController::class, 'enviarUno'])->middleware('throttle:30,1')->name('resumenes.enviar-uno');
        Route::get('/resumenes/{resumen}/pdf', [ResumenController::class, 'verPdf'])->name('resumenes.pdf');
        Route::delete('/resumenes/{resumen}', [ResumenController::class, 'destroy'])->name('resumenes.destroy');

        // Clientes — admin y empleados (no turnero)
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    });

    // Usuarios — solo admin
    Route::middleware(['admin'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::patch('/admin/usuarios/{usuario}/estado', [AdminBoxController::class, 'updateEstado'])->name('admin.usuarios.estado');
    });
});

require __DIR__.'/auth.php';