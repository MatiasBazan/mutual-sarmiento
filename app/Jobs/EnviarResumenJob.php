<?php

namespace App\Jobs;

use App\Events\ResumenProgreso;
use App\Models\Resumen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EnviarResumenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public int $resumenId,
    ) {}

    public function handle(): void
    {
        $resumen = Resumen::with('cliente')->findOrFail($this->resumenId);
        $cliente = $resumen->cliente;

        // Marcar como enviando
        $resumen->update(['estado' => Resumen::ENVIANDO, 'intentos' => $resumen->intentos + 1]);

        $this->broadcast($resumen, Resumen::ENVIANDO);

        // Sin celular
        if (empty($cliente->celular)) {
            $resumen->update(['estado' => Resumen::SIN_CELULAR]);
            $this->broadcast($resumen, Resumen::SIN_CELULAR);
            return;
        }

        try {
            $this->enviarWhatsApp($cliente->celular, $resumen->pdf_path);

            // Borrar PDF del disco público
            if ($resumen->pdf_path && Storage::exists($resumen->pdf_path)) {
                Storage::delete($resumen->pdf_path);
            }

            $resumen->update([
                'estado'     => Resumen::NOTIFICADO,
                'enviado_at' => now(),
                'pdf_path'   => null,
            ]);

            $this->broadcast($resumen, Resumen::NOTIFICADO);
        } catch (\Throwable $e) {
            Log::error("Error enviando resumen {$this->resumenId}: " . preg_replace('/\+?\d{7,}/', '[PHONE]', $e->getMessage()));
            $resumen->update(['estado' => Resumen::ERROR]);
            $this->broadcast($resumen, Resumen::ERROR);
        }
    }

    private function enviarWhatsApp(string $celular, string $pdfPath): void
    {
        $instanceId  = config('services.zapi.instance_id');
        $token       = config('services.zapi.token');
        $clientToken = config('services.zapi.client_token');
        $numero      = $this->normalizarCelular($celular);

        $fullPath = storage_path('app/private/' . $pdfPath);
        if (!file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $pdfPath);
        }
        if (!file_exists($fullPath)) {
            throw new \Exception('PDF no encontrado en: ' . $fullPath);
        }

        $nombreArchivo = 'resumen_' . now()->format('Y-m') . '.pdf';

        // Copiar al disco público para que Z-API pueda acceder por URL
        $nombrePublico = 'temp/' . basename($pdfPath);
        Storage::disk('public')->put($nombrePublico, file_get_contents($fullPath));
        $pdfUrl = Storage::disk('public')->url($nombrePublico);

        Log::info('Enviando PDF via URL', [
            'path'    => $pdfPath,
            'url'     => $pdfUrl,
            'size'    => filesize($fullPath),
            'numero'  => $numero,
        ]);

        $headers = ['Content-Type' => 'application/json'];
        if (!empty($clientToken)) {
            $headers['Client-Token'] = $clientToken;
        }

        try {
            $response = Http::withHeaders($headers)
                ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
                ->post("https://api.z-api.io/instances/{$instanceId}/token/{$token}/send-document/pdf", [
                    'phone'    => $numero,
                    'document' => $pdfUrl,
                    'fileName' => $nombreArchivo,
                    'caption'  => 'Mutual Club Sarmiento — Su resumen mensual adjunto.',
                ]);
        } finally {
            // Borrar temp independientemente de éxito o error
            Storage::disk('public')->delete($nombrePublico);
        }

        if (!$response->successful()) {
            throw new \Exception('Z-API error: ' . $response->body());
        }
    }

    private function normalizarCelular(string $celular): string
    {
        $numero = preg_replace('/\D/', '', $celular);

        if (str_starts_with($numero, '0')) {
            $numero = substr($numero, 1);
        }
        if (str_starts_with($numero, '15')) {
            $numero = substr($numero, 2);
        }
        if (!str_starts_with($numero, '549')) {
            $numero = '549' . $numero;
        }

        return $numero;
    }

    private function broadcast(Resumen $resumen, string $estado): void
    {
        $periodo = $resumen->periodo;

        $total = Resumen::where('periodo', $periodo)->count();

        $procesados = Resumen::where('periodo', $periodo)
            ->whereIn('estado', [Resumen::NOTIFICADO, Resumen::ERROR, Resumen::SIN_CELULAR])
            ->count();

        try {
            broadcast(new ResumenProgreso(
                resumenId:     $resumen->id,
                estado:        $estado,
                clienteNombre: $resumen->cliente->nombre_completo,
                total:         $total,
                procesados:    $procesados,
            ));
        } catch (\Exception $e) {
            Log::warning('Reverb broadcast falló: ' . $e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        $resumen = Resumen::with('cliente')->find($this->resumenId);
        if (!$resumen) {
            return;
        }

        $resumen->update(['estado' => Resumen::ERROR]);

        $periodo    = $resumen->periodo;
        $total      = Resumen::where('periodo', $periodo)->count();
        $procesados = Resumen::where('periodo', $periodo)
            ->whereIn('estado', [Resumen::NOTIFICADO, Resumen::ERROR, Resumen::SIN_CELULAR])
            ->count();

        try {
            broadcast(new ResumenProgreso(
                resumenId:     $resumen->id,
                estado:        Resumen::ERROR,
                clienteNombre: $resumen->cliente->nombre_completo ?? '',
                total:         $total,
                procesados:    $procesados,
            ));
        } catch (\Exception $e) {
            Log::warning('Reverb broadcast falló en failed(): ' . $e->getMessage());
        }
    }
}
