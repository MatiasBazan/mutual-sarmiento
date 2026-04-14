<?php

namespace Tests\Feature;

use App\Http\Controllers\ResumenController;
use Tests\TestCase;

class PdfParserTest extends TestCase
{
    /** @test */
    public function extrae_nombre_completo_del_pdf(): void
    {
        $datos = $this->parsear($this->getFixturePdf(), 'GOMEZ');

        $this->assertEquals('GOMEZ GUSTAVO ESTEBAN', $datos['nombre_completo']);
    }

    /** @test */
    public function extrae_numero_de_socio_correctamente(): void
    {
        $datos = $this->parsear($this->getFixturePdf(), 'GOMEZ');

        $this->assertEquals('0001-00026978', $datos['nro_socio']);
    }

    /** @test */
    public function no_confunde_nro_socio_con_celular(): void
    {
        $datos = $this->parsear($this->getFixturePdf(), 'GOMEZ');

        // El celular NO debe ser igual al nro de resumen
        $this->assertNotEquals('0001-00026978', $datos['celular'] ?? '');
    }

    /** @test */
    public function extrae_direccion_correctamente(): void
    {
        $datos = $this->parsear($this->getFixturePdf(), 'GOMEZ');

        $this->assertArrayHasKey('direccion', $datos);
        $this->assertStringContainsString('ISABEL', $datos['direccion']);
    }

    /** @test */
    public function el_celular_extraido_es_numerico(): void
    {
        $datos = $this->parsear($this->getFixturePdf(), 'GOMEZ');

        if (isset($datos['celular'])) {
            $this->assertMatchesRegularExpression('/^\d+$/', $datos['celular']);
        } else {
            $this->markTestSkipped('No se extrajo celular del fixture.');
        }
    }

    /** @test */
    public function nombre_completo_empieza_con_apellido(): void
    {
        $datos = $this->parsear($this->getFixturePdf(), 'GOMEZ');

        $this->assertStringStartsWith('GOMEZ', $datos['nombre_completo'] ?? '');
    }

    // -------------------------------------------------------------------------

    private function getFixturePdf(): string
    {
        // Texto representativo extraído del PDF de Gomez (fixture hardcodeado)
        return "31/03/2026\n0001-00026978\nGOMEZ GUSTAVO ESTEBAN\n" .
               "ISABEL 1644\nPag. 1\n2594 - LEONES\n12 883 3010\n" .
               "Este resumen se considera ACEPTADO\n" .
               "0.00 1,555,807.92 8/04/2026\n" .
               "13/03/2026 -1,000,000.00 Su Pago\n" .
               "Sub-Total--->GOMEZ GUSTAVO ESTEBAN 338,267.75\n" .
               "900,185.50 450,092.75\n" .
               "100.00 6,009.83 900,185.50\n" .
               "12 883 3010 0001-00026978 GOMEZ GUSTAVO ESTEBAN";
    }

    private function parsear(string $texto, string $apellido): array
    {
        $controller = new ResumenController();
        $reflection = new \ReflectionMethod($controller, 'parsearPdf');
        $reflection->setAccessible(true);

        return $reflection->invoke($controller, $texto, $apellido);
    }
}