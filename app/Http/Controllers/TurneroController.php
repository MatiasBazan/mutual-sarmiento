<?php

namespace App\Http\Controllers;

use App\Events\BoxStatusUpdated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TurneroController extends Controller
{
    /**
     * Pantalla TV — pública, sin auth.
     */
    public function tv()
    {
        $usuarios = User::orderBy('role', 'desc') // admin primero
                        ->orderBy('id')
                        ->get(['id', 'name', 'box_nombre', 'box_status', 'status_changed_at']);

        return view('turnero.tv', compact('usuarios'));
    }

    /**
     * Panel del empleado — requiere auth.
     */
    public function panel()
    {
        return view('turnero.panel', ['user' => auth()->user()]);
    }

    /**
     * Cambia el estado del box del usuario autenticado.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'box_status' => ['required', 'in:libre,ocupado,pausa,ausente'],
        ]);

        $user = $request->user();
        $user->update([
            'box_status'        => $request->box_status,
            'status_changed_at' => now(),
        ]);

        try {
            broadcast(new BoxStatusUpdated($user))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Reverb no disponible: ' . $e->getMessage());
        }

        return response()->json(['ok' => true, 'box_status' => $user->box_status]);
    }
}
