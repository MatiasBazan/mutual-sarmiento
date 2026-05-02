<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    private string $token;
    private string $phoneNumberId;

    public function __construct()
    {
        $this->token         = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Envía un PDF como documento usando la plantilla "resumen_mensual" de Meta.
     * El PDF debe existir en storage (local o private). Se copia temporalmente
     * al disco público para que Meta pueda descargarlo via URL, y se borra al finalizar.
     */
    public function sendDocument(string $celular, string $pdfPath, string $fileName): void
    {
        $numero   = $this->normalizarCelular($celular);
        $fullPath = $this->resolverRutaPdf($pdfPath);

        $nombrePublico = 'temp/' . basename($pdfPath);
        Storage::disk('public')->put($nombrePublico, file_get_contents($fullPath));
        $pdfUrl = Storage::disk('public')->url($nombrePublico);

        Log::info('Enviando PDF via Meta WhatsApp Cloud API', [
            'numero'   => preg_replace('/\d{4}$/', '****', $numero),
            'fileName' => $fileName,
            'size'     => filesize($fullPath),
        ]);

        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages", [
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
                                            'filename' => $fileName,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
        } finally {
            Storage::disk('public')->delete($nombrePublico);
        }

        Log::info('Meta WhatsApp respuesta', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Meta WhatsApp error ' . $response->status() . ': ' . $response->body());
        }
    }

    private function resolverRutaPdf(string $pdfPath): string
    {
        foreach (['app/private/', 'app/'] as $base) {
            $path = storage_path($base . $pdfPath);
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \Exception('PDF no encontrado: ' . $pdfPath);
    }

    private function normalizarCelular(string $celular): string
    {
        $numero = preg_replace('/\D/', '', $celular);

        if (str_starts_with($numero, '549')) {
            return '+' . $numero;
        }

        if (str_starts_with($numero, '54')) {
            return '+549' . substr($numero, 2);
        }

        if (str_starts_with($numero, '0')) {
            $numero = substr($numero, 1);
        }

        if (str_starts_with($numero, '15')) {
            $numero = substr($numero, 2);
        }

        return '+549' . $numero;
    }
}
