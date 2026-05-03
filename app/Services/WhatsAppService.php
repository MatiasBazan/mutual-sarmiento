<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendDocument(
        string $telefono,
        string $pdfUrl,
        string $nombre,
        string $periodo,
        string $fechaVencimiento
    ): bool {
        $token   = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_number_id');
        $version = config('services.whatsapp.version', 'v25.0');

        if (empty($token) || empty($phoneId)) {
            Log::error('WhatsApp credenciales faltantes (token/phone_number_id).');
            return false;
        }

        $numero  = $this->normalizarCelular($telefono);
        $url     = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $numero,
            'type'              => 'template',
            'template'          => [
                'name'       => 'resumen_mensual',
                'language'   => ['code' => 'es_AR'],
                'components' => [
                    [
                        'type'       => 'header',
                        'parameters' => [
                            [
                                'type'     => 'document',
                                'document' => [
                                    'link'     => $pdfUrl,
                                    'filename' => 'Resumen.pdf',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'       => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $nombre],
                            ['type' => 'text', 'text' => $periodo],
                            ['type' => 'text', 'text' => $fechaVencimiento],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($token)
                ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
                ->acceptJson()
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::error('WhatsApp sendDocument error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'numero' => $numero,
                ]);
                return false;
            }

            Log::info('WhatsApp sendDocument ok', [
                'numero' => $numero,
                'msg_id' => data_get($response->json(), 'messages.0.id'),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp sendDocument excepción: ' . $e->getMessage(), ['numero' => $numero ?? null]);
            return false;
        }
    }

    public function normalizarCelular(string $celular): string
    {
        // 1. Eliminar espacios, guiones, paréntesis y cualquier no-dígito
        $numero = preg_replace('/\D/', '', $celular);

        // 2. Eliminar 0 inicial
        if (str_starts_with($numero, '0')) {
            $numero = substr($numero, 1);
        }

        // 3. Agregar prefijo 549 si no lo tiene
        if (!str_starts_with($numero, '549')) {
            $numero = '549' . $numero;
        }

        // 4. Agregar + al principio
        $normalizado = '+' . $numero;

        Log::info('WhatsApp normalizarCelular', [
            'original'    => $celular,
            'normalizado' => $normalizado,
        ]);

        return $normalizado;
    }
}
