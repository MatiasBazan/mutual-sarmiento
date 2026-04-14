<?php

namespace App\Jobs;

use App\Events\ResumenProgreso;
use App\Models\Cliente;
use App\Models\Resumen;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Http\GuzzleClient as TwilioGuzzleClient;
use Twilio\Rest\Client as TwilioClient;

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
            $this->enviarWhatsApp($cliente, $resumen);

            // Borrar PDF del disco público
            if ($resumen->pdf_path && Storage::disk('public')->exists($resumen->pdf_path)) {
                Storage::disk('public')->delete($resumen->pdf_path);
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

    private function enviarWhatsApp(Cliente $cliente, Resumen $resumen): void
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.whatsapp_from');

        $to     = Cliente::normalizarCelular($cliente->celular);
        $pdfUrl = url(Storage::disk('public')->url($resumen->pdf_path));
        $body   = "Hola {$cliente->nombre_completo}, te enviamos tu resumen de cuenta del período {$resumen->periodo}.";

        // --- MODO DESARROLLO: simular envío si Twilio no está configurado ---
        if (empty($sid) || empty($token)) {
            $toMasked = preg_replace('/\d(?=\d{4})/', '*', $to);
            Log::info('[Twilio DEV] Envío simulado', [
                'resumen_id' => $resumen->id,
                'cliente'    => $cliente->nombre_completo,
                'to'         => $toMasked,
                'body'       => $body,
            ]);
            return;
        }

        // --- PRODUCCIÓN ---
        // En Windows, curl no encuentra el certificado CA del sistema para HTTPS.
        // Se configura Guzzle con verify:false solo en local para evitar el error:
        // "SSL certificate problem: unable to get local issuer certificate"
        $httpClient = new TwilioGuzzleClient(
            new GuzzleClient([
                'verify' => app()->environment('local') ? false : true,
            ])
        );

        $twilio = new TwilioClient($sid, $token, null, null, $httpClient);

        $twilio->messages->create($to, [
            'from'     => $from,
            'body'     => $body,
            'mediaUrl' => [$pdfUrl],
        ]);
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
