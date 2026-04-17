<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfGeneratorService
{
    /**
     * Genera un PDF profesional con los datos parseados del resumen y lo
     * guarda en storage/app/resumenes/{periodo}/.
     *
     * @param  array  $datos   Datos parseados del PDF original.
     * @param  string $periodo Período en formato Y-m, ej: "2026-04".
     * @return string          Path relativo guardado en Storage (disco local).
     */
    public function generarResumen(array $datos, string $periodo): string
    {
        \Log::info('=== PdfGeneratorService recibe ===', array_merge(
            array_diff_key($datos, ['movimientos' => null]),
            ['movimientos_count' => count($datos['movimientos'] ?? [])]
        ));

        $logoPath   = public_path('images/logo-credicas.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoMime   = mime_content_type($logoPath);
            $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('pdf.resumen', array_merge($datos, [
            'logoBase64' => $logoBase64,
        ]))->setPaper('a4', 'portrait');

        $apellidoSlug = Str::slug($datos['apellido'] ?? 'resumen');

        $filename = 'resumen_' . $apellidoSlug . '_' . $periodo . '.pdf';
        $path     = 'resumenes/' . $periodo . '/' . $filename;

        Storage::put($path, $pdf->output());

        return $path;
    }
}
