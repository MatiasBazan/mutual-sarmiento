<?php

namespace Tests\Unit;

use App\Services\PdfGeneratorService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfGeneratorTest extends TestCase
{
    private function datosMuestra(): array
    {
        return [
            'nombre_completo'   => 'GOMEZ GUSTAVO ESTEBAN',
            'apellido'          => 'GOMEZ',
            'nro_socio'         => '0001-00026978',
            'direccion'         => 'ISABEL 1644',
            'localidad'         => '2594 - LEONES',
            'telefono'          => '12 883 3010',
            'fecha_periodo'     => '31/03/2026',
            'fecha_vencimiento' => '8/04/2026',
            'saldo_anterior'    => '0.00',
            'total_pagar'       => '1,555,807.92',
            'pago_minimo'       => '450,092.75',
            'consumo_actual'    => '338,267.75',
            'intereses'         => '6,009.83',
            'total_financiado'  => '900,185.50',
            'movimientos'       => [
                [
                    'fecha'       => '13/03/2026',
                    'comprobante' => '',
                    'descripcion' => 'Su Pago',
                    'cuota'       => '',
                    'importe'     => '-1,000,000.00',
                ],
            ],
        ];
    }

    /** @test */
    public function genera_pdf_y_lo_guarda_en_storage(): void
    {
        Storage::fake('local');

        $service = new PdfGeneratorService();
        $path    = $service->generarResumen($this->datosMuestra(), '2026-04');

        Storage::assertExists($path);
        $this->assertStringEndsWith('.pdf', $path);
    }

    /** @test */
    public function el_nombre_del_pdf_incluye_apellido_y_periodo(): void
    {
        Storage::fake('local');

        $service = new PdfGeneratorService();
        $path    = $service->generarResumen($this->datosMuestra(), '2026-04');

        $this->assertStringContainsString('gomez', $path);
        $this->assertStringContainsString('2026-04', $path);
    }

    /** @test */
    public function el_pdf_se_guarda_en_la_carpeta_del_periodo(): void
    {
        Storage::fake('local');

        $service = new PdfGeneratorService();
        $path    = $service->generarResumen($this->datosMuestra(), '2026-04');

        $this->assertStringStartsWith('resumenes/2026-04/', $path);
    }

    /** @test */
    public function maneja_apellido_con_tildes_o_espacios(): void
    {
        Storage::fake('local');

        $datos          = $this->datosMuestra();
        $datos['apellido'] = 'GARCÍA LÓPEZ';

        $service = new PdfGeneratorService();
        $path    = $service->generarResumen($datos, '2026-04');

        // El slug no debe tener espacios ni caracteres especiales
        $this->assertStringNotContainsString(' ', $path);
        Storage::assertExists($path);
    }
}