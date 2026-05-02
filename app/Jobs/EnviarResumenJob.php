<?php

namespace App\Jobs;

use App\Events\ResumenProgreso;
use App\Models\Resumen;
use App\Services\WhatsAppService;
use Carbon\Carbon;
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
    public int $backoff = 60;

    public function __construct(
        public int $resumenId,
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $resumen = Resumen::with('cliente')->findOrFail($this->resumenId);
        $cliente = $resumen->cliente;

        $resumen->update(['estado' => Resumen::ENVIANDO, 'intentos' => $resumen->intentos + 1]);
        $this->broadcast($resumen, Resumen::ENVIANDO);

        $fullPath = storage_path('app/private/' . $resumen->pdf_path);
        if (!file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $resumen->pdf_path);
        }
        if (!file_exists($fullPath)) {
            Log::error("PDF no encontrado para resumen {$this->resumenId}", ['path' => $resumen->pdf_path]);
            throw new \Exception('PDF no encontrado');
        }

        // Copiar a disco público para que Meta pueda descargarlo via URL
        $nombrePublico = 'temp/' . basename($resumen->pdf_path);
        Storage::disk('public')->put($nombrePublico, file_get_contents($fullPath));
        $pdfUrl = Storage::disk('public')->url($nombrePublico);

        $periodoLegible   = $this->formatearPeriodo($resumen->periodo);
        $fechaVencimiento = $this->calcularFechaVencimiento($resumen->periodo);

        try {
            $ok = $whatsapp->sendDocument(
                telefono:         $cliente->celular,
                pdfUrl:           $pdfUrl,
                nombre:           $cliente->nombre_completo,
                periodo:          $periodoLegible,
                fechaVencimiento: $fechaVencimiento,
            );
        } finally {
            Storage::disk('public')->delete($nombrePublico);
        }

        if (!$ok) {
            Log::error("Error enviando resumen {$this->resumenId}: WhatsApp devolvió false");
            $resumen->update(['estado' => Resumen::ERROR]);
            $this->broadcast($resumen, Resumen::ERROR);
            throw new \Exception('Envío WhatsApp falló — ver logs.');
        }

        if ($resumen->pdf_path && Storage::exists($resumen->pdf_path)) {
            Storage::delete($resumen->pdf_path);
        }

        $resumen->update([
            'estado'     => Resumen::NOTIFICADO,
            'enviado_at' => now(),
            'pdf_path'   => null,
        ]);

        $this->broadcast($resumen, Resumen::NOTIFICADO);
    }

    private function formatearPeriodo(string $periodo): string
    {
        try {
            return Carbon::createFromFormat('Y-m', $periodo)
                ->locale('es')
                ->isoFormat('MMMM YYYY');
        } catch (\Throwable) {
            return $periodo;
        }
    }

    /** Vencimiento estándar: día 10 del mes siguiente al período. */
    private function calcularFechaVencimiento(string $periodo): string
    {
        try {
            return Carbon::createFromFormat('Y-m', $periodo)
                ->addMonthNoOverflow()
                ->day(10)
                ->format('d/m/Y');
        } catch (\Throwable) {
            return now()->addMonth()->day(10)->format('d/m/Y');
        }
    }

    private function broadcast(Resumen $resumen, string $estado): void
    {
        $periodo    = $resumen->periodo;
        $total      = Resumen::where('periodo', $periodo)->count();
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
