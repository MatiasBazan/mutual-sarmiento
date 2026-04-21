<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarResumenJob;
use App\Models\Cliente;
use App\Models\Resumen;
use App\Services\PdfGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResumenController extends Controller
{
    public function index(Request $request)
    {
        $periodoActual = now()->format('Y-m');

        $q       = trim((string) $request->query('q', ''));
        $celular = $request->query('celular', 'todos');
        if (!in_array($celular, ['todos', 'con', 'sin'], true)) {
            $celular = 'todos';
        }

        // Escapar wildcards de LIKE para que %_\ se busquen como literales.
        $qEscaped = addcslashes($q, '%_\\');

        $baseQuery = Resumen::query()
            ->where('periodo', $periodoActual)
            ->when($q !== '', function ($query) use ($qEscaped) {
                $query->whereHas('cliente', function ($qb) use ($qEscaped) {
                    $qb->where('nombre_completo', 'like', "%{$qEscaped}%")
                       ->orWhere('apellido', 'like', "%{$qEscaped}%");
                });
            })
            ->when($celular === 'con', fn($query) =>
                $query->whereHas('cliente', fn($qb) =>
                    $qb->whereNotNull('celular')->where('celular', '<>', '')
                )
            )
            ->when($celular === 'sin', fn($query) =>
                $query->whereHas('cliente', fn($qb) =>
                    $qb->where(fn($w) => $w->whereNull('celular')->orWhere('celular', ''))
                )
            );

        $resumenes = (clone $baseQuery)
            ->with('cliente')
            ->orderBy('estado')
            ->orderBy('id')
            ->paginate(30)
            ->withQueryString();

        // Stats reflejan los filtros activos.
        $stats = (clone $baseQuery)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');


        return view('resumenes.index', compact('resumenes', 'stats', 'periodoActual', 'q', 'celular'));
    }

    public function importar(Request $request)
    {
        $request->validate([
            'pdfs'   => ['required', 'array', 'min:1'],
            'pdfs.*' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $periodo    = now()->format('Y-m');
        $importados = 0;
        $errores    = [];

        $pdfService = new PdfGeneratorService();

        foreach ($request->file('pdfs') as $archivo) {
            $apellido = strtoupper(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));

            try {
                // Guardar temporalmente en disco público para extraer el texto
                $nombreTemp = Str::uuid() . '.pdf';
                $archivo->storeAs("resumenes/{$periodo}", $nombreTemp, 'public');
                $fullPath = Storage::disk('public')->path("resumenes/{$periodo}/{$nombreTemp}");

                // Extraer texto del PDF original
                $texto = $this->extraerTextoPdf($fullPath);

                // Ya no necesitamos el PDF original — lo eliminamos
                Storage::disk('public')->delete("resumenes/{$periodo}/{$nombreTemp}");

                if (empty(trim($texto))) {
                    $errores[] = "{$apellido}: no se pudo leer el PDF";
                    continue;
                }

                // Log del texto RAW para diagnóstico de regex
                \Log::info("=== TEXTO RAW DEL PDF [{$apellido}] ===", [
                    'total_chars' => strlen($texto),
                    'lineas'      => array_slice(explode("\n", $texto), 0, 50),
                ]);

                // Parsear campos del PDF original
                $datos = $this->parsearPdf($texto, $apellido);
                $datos['apellido'] = $apellido;

                // BUG 5 FIX — usar nro_socio como clave única si está disponible,
                // para evitar mezclar clientes con el mismo apellido
                $nroSocio = $datos['nro_socio'] ?? null;

                // celular NO se incluye: el teléfono del PDF es de la mutual,
                // no del socio. Si el cliente ya existe con celular cargado,
                // array_filter garantiza que no se pise con null.
                $camposCliente = array_filter([
                    'apellido'        => $apellido,
                    'nombre_completo' => $datos['nombre_completo'] ?? null,
                    'direccion'       => $datos['direccion']       ?? null,
                    'localidad'       => $datos['localidad']       ?? null,
                    'ultimo_periodo'  => $periodo,
                ], fn($v) => $v !== null && $v !== '');

                if ($nroSocio) {
                    $cliente = Cliente::updateOrCreate(
                        ['nro_socio' => $nroSocio],
                        $camposCliente
                    );
                } else {
                    // Fallback por apellido si no se pudo extraer nro_socio
                    $cliente = Cliente::updateOrCreate(
                        ['apellido' => $apellido],
                        $camposCliente
                    );
                }

                // Log para diagnóstico — ver qué datos llegan al generador de PDF
                \Log::info('=== DATOS PARA PDF ===', [
                    'apellido'          => $datos['apellido']          ?? 'NO EXISTE',
                    'limite_compra'     => $datos['limite_compra']     ?? 'NO EXISTE',
                    'saldo_anterior'    => $datos['saldo_anterior']    ?? 'NO EXISTE',
                    'pago_minimo'       => $datos['pago_minimo']       ?? 'NO EXISTE',
                    'consumo_actual'    => $datos['consumo_actual']    ?? 'NO EXISTE',
                    'sellado'           => $datos['sellado']           ?? 'NO EXISTE',
                    'gastos_resumen'    => $datos['gastos_resumen']    ?? 'NO EXISTE',
                    'saldo_actual'      => $datos['saldo_actual']      ?? 'NO EXISTE',
                    'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? 'NO EXISTE',
                    'movimientos_count' => count($datos['movimientos'] ?? []),
                    'movimientos'       => $datos['movimientos']       ?? [],
                ]);

                // Generar PDF nuevo con diseño profesional y guardarlo en storage local
                $nuevoPdfPath = $pdfService->generarResumen($datos, $periodo);

                // Upsert resumen — si ya existe para este período, actualiza el PDF nuevo y resetea a pendiente
                Resumen::updateOrCreate(
                    ['cliente_id' => $cliente->id, 'periodo' => $periodo],
                    ['pdf_path' => $nuevoPdfPath, 'estado' => Resumen::PENDIENTE, 'enviado_at' => null, 'intentos' => 0]
                );

                $importados++;
            } catch (\Throwable $e) {
                $errores[] = "{$apellido}: " . $e->getMessage();
            }
        }

        $msg = "Se importaron {$importados} resúmenes para {$periodo}.";
        if (!empty($errores)) {
            $msg .= ' Problemas: ' . implode(' | ', $errores);
        }

        return redirect()->route('resumenes.index')->with('success', $msg);
    }

    public function enviarTodos()
    {
        $periodo = now()->format('Y-m');

        // Incluye pendientes + error (reintento) — excluye notificado
        $resumenes = Resumen::with('cliente')
            ->where('periodo', $periodo)
            ->whereIn('estado', [Resumen::PENDIENTE, Resumen::ERROR])
            ->get();

        if ($resumenes->isEmpty()) {
            return response()->json([
                'message' => 'No hay resúmenes pendientes ni con error para este período.',
            ], 422);
        }

        // Separar los que tienen celular de los que no
        $conCelular    = $resumenes->filter(fn($r) => !empty($r->cliente->celular));
        $sinCelular    = $resumenes->filter(fn($r) =>  empty($r->cliente->celular));

        if ($conCelular->isEmpty()) {
            return response()->json([
                'message' => 'Todos los clientes pendientes no tienen celular registrado.',
            ], 422);
        }

        // Resetear los que estaban en error a pendiente antes de despachar
        $conCelular->each(function ($r) {
            if ($r->estado === Resumen::ERROR) {
                $r->update(['estado' => Resumen::PENDIENTE, 'intentos' => 0]);
            }
        });

        $total = $conCelular->count();

        foreach ($conCelular as $resumen) {
            EnviarResumenJob::dispatch($resumen->id);
        }

        return response()->json([
            'message'     => "Se despacharon {$total} trabajos de envío.",
            'total'       => $total,
            'sin_celular' => $sinCelular->map(fn($r) => $r->cliente->nombre_completo)->values(),
        ]);
    }

    public function enviarUno(Resumen $resumen)
    {
        if ($resumen->estado === Resumen::NOTIFICADO) {
            return response()->json(['message' => 'Este resumen ya fue enviado.'], 422);
        }

        if (!$resumen->cliente->celular) {
            return response()->json(['message' => 'El cliente no tiene celular registrado.'], 422);
        }

        $resumen->update(['estado' => Resumen::PENDIENTE, 'intentos' => 0]);
        EnviarResumenJob::dispatch($resumen->id);

        return response()->json(['ok' => true, 'message' => 'Resumen encolado para envío.']);
    }

    public function destroy(Resumen $resumen)
    {
        if ($resumen->pdf_path && Storage::exists($resumen->pdf_path)) {
            Storage::delete($resumen->pdf_path);
        }

        $resumen->delete();

        return response()->json(['ok' => true]);
    }

    public function verPdf(Resumen $resumen)
    {
        if (!Storage::exists($resumen->pdf_path)) {
            abort(404, 'PDF no encontrado');
        }

        return response()->file(
            Storage::path($resumen->pdf_path),
            ['Content-Type' => 'application/pdf']
        );
    }

    // -------------------------------------------------------------------------
    // PDF — extracción de texto
    // -------------------------------------------------------------------------

    private function extraerTextoPdf(string $path): string
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($path);
        return $pdf->getText();
    }

    // -------------------------------------------------------------------------
    // PDF — parser completo para el formato real de la mutual
    //
    // Estructura del texto extraído (pdftotext):
    //   31/03/2026                               ← fecha_periodo
    //   0001-00026978                            ← nro_socio
    //   GOMEZ GUSTAVO ESTEBAN                    ← nombre_completo
    //   ISABEL 1644                              ← direccion
    //   Pag. 1
    //   2594 - LEONES                            ← localidad
    //   12 883 3010                              ← celular / telefono
    //   Este resumen se considera ACEPTADO
    //   0.00 1,555,807.92 8/04/2026              ← saldo_anterior, total_pagar, fecha_vencimiento
    //   13/03/2026 -1,000,000.00 Su Pago         ← movimiento (formato B: fecha importe descripcion)
    //   Sub-Total--->GOMEZ GUSTAVO ESTEBAN 338,267.75  ← consumo_actual
    //   900,185.50 450,092.75                    ← total_financiado, pago_minimo
    //   100.00 6,009.83 900,185.50               ← [X], intereses, total_financiado
    // -------------------------------------------------------------------------

    private function parsearPdf(string $texto, string $apellido): array
    {
        $datos  = [];
        $lineas = array_values(array_filter(array_map('trim', explode("\n", $texto))));
        $count  = count($lineas);

        // ── Índices fijos (estructura conocida del PDF de la mutual) ──────────
        // [0] "31/03/2026"
        // [1] "0001-00026978"
        // [2] "GOMEZ GUSTAVO ESTEBAN"
        // [3] "ISABEL 1644"
        // [4] "Pag.   1"  ← ignorar
        // [5] "2594 - LEONES"
        // [6] "12 883 3010"
        $datos['fecha_periodo']   = $lineas[0] ?? '';
        $datos['nro_socio']       = $lineas[1] ?? '';
        $datos['nombre_completo'] = $lineas[2] ?? '';
        $datos['direccion']       = $lineas[3] ?? '';
        $datos['localidad']       = $lineas[5] ?? '';
        $datos['nro_tarjeta']     = $lineas[6] ?? '';

        // ── Línea [10]: "0.00 1,555,807.92 8/04/2026" ────────────────────────
        //   limite_compra = 0.00  |  saldo_anterior = 1,555,807.92  |  fecha_vencimiento
        if (isset($lineas[10]) && preg_match(
            '/^([\d,\.]+)\s+([\d,\.]+)\s+(\d{1,2}\/\d{2}\/\d{4})$/',
            $lineas[10], $m
        )) {
            $datos['limite_compra']     = $m[1];
            $datos['saldo_anterior']    = $m[2];
            $datos['fecha_vencimiento'] = $m[3];
        }

        // ── Línea Sub-Total: "338,267.75Sub-Total--->NOMBRE" ─────────────────
        $subTotalIdx = null;
        foreach ($lineas as $idx => $linea) {
            if (str_contains($linea, 'Sub-Total')) {
                $subTotalIdx = $idx;
                break;
            }
        }

        if ($subTotalIdx !== null) {
            // consumo_actual al INICIO de la línea
            if (preg_match('/^([\d,\.]+)Sub-Total/', $lineas[$subTotalIdx], $m)) {
                $datos['consumo_actual'] = $m[1];
            }

            // [$subTotalIdx+1]: "900,185.50 450,092.75"  → saldo_actual, pago_minimo
            $post1 = $lineas[$subTotalIdx + 1] ?? '';
            if (preg_match('/^([\d,\.]+)\s+([\d,\.]+)$/', $post1, $m)) {
                $datos['saldo_actual'] = $m[1];
                $datos['pago_minimo']  = $m[2];
            }

            // [$subTotalIdx+2]: "100.00 6,009.83 900,185.50" → gastos_resumen, sellado
            $post2 = $lineas[$subTotalIdx + 2] ?? '';
            if (preg_match('/^([\d,\.]+)\s+([\d,\.]+)\s+[\d,\.]+$/', $post2, $m)) {
                $datos['gastos_resumen'] = $m[1];
                $datos['sellado']        = $m[2];
            }
        }

        // ── "Consumo Actual: 338,267.75" — confirma/sobreescribe consumo_actual ─
        foreach ($lineas as $linea) {
            if (preg_match('/Consumo Actual:\s+([\d,\.]+)/', $linea, $m)) {
                $datos['consumo_actual'] = $m[1];
                break;
            }
        }

        // ── Movimientos (líneas 11..Sub-Total-1) ─────────────────────────────
        // Patrones en orden de especificidad:
        //   D: "fecha -importe descripcion"           → pago negativo
        //   A: "fecha comprobante cuota importe[desc]" → con cuota NN-NN
        //   B: "fecha comprobante importe[desc]"       → sin cuota
        //   C: "fecha TextoAlfa importe[desc]"         → texto como comprobante
        $movimientos = [];
        $limiteMovs  = $subTotalIdx ?? $count;

        for ($i = 11; $i < $limiteMovs; $i++) {
            $linea = $lineas[$i];
            if (!preg_match('/^\d{1,2}\/\d{2}\/\d{4}\s/', $linea)) {
                continue;
            }

            // PATRÓN D — pago negativo: "13/03/2026 -1,000,000.00 Su Pago"
            if (preg_match(
                '/^(\d{1,2}\/\d{2}\/\d{4})\s+(-[\d,\.]+)\s+(.+)$/',
                $linea, $m
            )) {
                $movimientos[] = [
                    'fecha'       => $m[1],
                    'comprobante' => '',
                    'cuota'       => '',
                    'importe'     => $m[2],
                    'descripcion' => trim($m[3]),
                ];
                continue;
            }

            // PATRÓN A — con cuota: "7/01/2026 285449 03-03 42,700.00ELITE"
            if (preg_match(
                '/^(\d{1,2}\/\d{2}\/\d{4})\s+(\d+)\s+(\d{2}-\d{2})\s+([\d,\.]+)(.*)$/',
                $linea, $m
            )) {
                $movimientos[] = [
                    'fecha'       => $m[1],
                    'comprobante' => $m[2],
                    'cuota'       => $m[3],
                    'importe'     => $m[4],
                    'descripcion' => trim($m[5]),
                ];
                continue;
            }

            // PATRÓN B — sin cuota: "4/03/2026 297399 59,308.00 ATALAYA S.R.L."
            if (preg_match(
                '/^(\d{1,2}\/\d{2}\/\d{4})\s+(\d+)\s+([\d,\.]+)\s*(.*)$/',
                $linea, $m
            )) {
                $movimientos[] = [
                    'fecha'       => $m[1],
                    'comprobante' => $m[2],
                    'cuota'       => '',
                    'importe'     => $m[3],
                    'descripcion' => trim($m[4]),
                ];
                continue;
            }

            // PATRÓN C — texto como comprobante: "13/03/2026 Debito 14,000.00 Cant.:..."
            if (preg_match(
                '/^(\d{1,2}\/\d{2}\/\d{4})\s+([A-Za-z]+)\s+([\d,\.]+)\s*(.*)$/',
                $linea, $m
            )) {
                $movimientos[] = [
                    'fecha'       => $m[1],
                    'comprobante' => $m[2],
                    'cuota'       => '',
                    'importe'     => $m[3],
                    'descripcion' => trim($m[4]),
                ];
                continue;
            }
        }

        if (!empty($movimientos)) {
            $datos['movimientos'] = $movimientos;
        }

        \Log::info('Datos parseados del PDF', array_merge(
            $datos,
            ['movimientos_count' => count($datos['movimientos'] ?? [])]
        ));

        return $datos;
    }
}
