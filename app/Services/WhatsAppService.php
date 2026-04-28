<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía el template "resumen_mensual" aprobado en Meta.
     * Header: PDF como document. Body: nombre, período, fecha vencimiento.
     */
    public function enviarTemplateResumen(
        string $celular,
        string $pdfUrl,
        string $nombreCliente,
        string $periodo,
        string $fechaVencimiento,
        string $filename = 'resumen_mensual.pdf',
        string $templateName = 'resumen_mensual',
        string $languageCode = 'es_AR'
    ): bool {
        $token   = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');
        $version = config('services.whatsapp.version', 'v25.0');

        if (empty($token) || empty($phoneId)) {
            Log::error('WhatsApp credenciales faltantes (token/phone_id).');
            return false;
        }

        $numero = $this->normalizarCelular($celular);
        $url    = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $numero,
            'type'              => 'template',
            'template' => [
                'name'     => $templateName,
                'language' => ['code' => $languageCode],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'document',
                                'document' => [
                                    'link'     => $pdfUrl,
                                    'filename' => $filename,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $nombreCliente],
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
                Log::error('WhatsApp template error', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                    'numero'   => $numero,
                    'template' => $templateName,
                ]);
                return false;
            }

            Log::info('WhatsApp template enviado', [
                'numero'   => $numero,
                'template' => $templateName,
                'msg_id'   => data_get($response->json(), 'messages.0.id'),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp template excepción: ' . $e->getMessage(), [
                'numero'   => $numero ?? null,
                'template' => $templateName,
            ]);
            return false;
        }
    }

    public function normalizarCelular(string $celular): string
    {
        $numero = preg_replace('/\D/', '', $celular);

        if (str_starts_with($numero, '549')) {
            return $numero;
        }

        if (str_starts_with($numero, '54')) {
            return '549' . substr($numero, 2);
        }

        if (str_starts_with($numero, '0')) {
            $numero = substr($numero, 1);
        }

        if (str_starts_with($numero, '15')) {
            $numero = substr($numero, 2);
        }

        return '549' . $numero;
    }
}
