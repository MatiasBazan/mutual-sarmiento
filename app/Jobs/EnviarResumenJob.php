<?php

namespace App\Jobs;

use App\Events\ResumenProgreso;
use App\Models\Resumen;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

        try {
            $fileName = 'resumen_' . now()->format('Y-m') . '.pdf';
            app(WhatsAppService::class)->sendDocument($cliente->celular, $resumen->pdf_path, $fileName);

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

    private function broadcast(Resumen $resumen, string $estado): void
    {
        $periodo = $resumen->periodo;

        $total = Resumen::where('periodo', $periodo)->count();

        $procesados = Resumen::where('periodo', $periodo)
            ->whereIn('estado', [Resumen::NOTIFICADO, Resumen::ERROR])
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
            ->whereIn('estado', [Resumen::NOTIFICADO, Resumen::ERROR])
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
