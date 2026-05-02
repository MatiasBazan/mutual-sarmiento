<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verificación de webhook requerida por Meta.
     * Meta hace GET con hub.mode=subscribe, hub.verify_token y hub.challenge.
     */
    public function verify(Request $request): Response
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Recibe eventos de Meta: delivery receipts, mensajes entrantes, etc.
     * Meta espera HTTP 200 inmediato; el procesamiento pesado va a una queue.
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        Log::info('WhatsApp webhook recibido', ['payload' => $payload]);

        // Delivery status updates
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                foreach ($value['statuses'] ?? [] as $status) {
                    Log::info('WhatsApp delivery status', [
                        'message_id' => $status['id']      ?? null,
                        'status'     => $status['status']  ?? null,
                        'recipient'  => preg_replace('/\d{4}$/', '****', $status['recipient_id'] ?? ''),
                        'timestamp'  => $status['timestamp'] ?? null,
                    ]);
                }
            }
        }

        return response('OK', 200);
    }
}
