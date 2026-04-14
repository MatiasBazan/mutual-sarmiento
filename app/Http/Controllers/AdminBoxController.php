<?php

namespace App\Http\Controllers;

use App\Events\BoxStatusUpdated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminBoxController extends Controller
{
    public function updateEstado(Request $request, User $usuario)
    {
        $request->validate([
            'status' => 'required|in:libre,ocupado,pausa,ausente',
        ]);

        $usuario->update([
            'box_status'        => $request->status,
            'status_changed_at' => now(),
        ]);

        try {
            broadcast(new BoxStatusUpdated($usuario))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Reverb no disponible: ' . $e->getMessage());
        }

        return response()->json(['ok' => true]);
    }
}
