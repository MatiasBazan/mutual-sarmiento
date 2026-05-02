<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Resumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ResumenPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $periodo = '2026-04';

        $cliente = Cliente::updateOrCreate(
            ['nro_socio' => '0001'],
            [
                'apellido'        => 'PRUEBA',
                'nombre_completo' => 'Cliente Prueba',
                'celular'         => '5493472548472',
                'direccion'       => 'Calle Falsa 123',
                'localidad'       => 'Leones',
                'ultimo_periodo'  => $periodo,
            ]
        );

        $pdfRelPath = 'resumenes/prueba.pdf';
        $html = '<h1>Resumen de prueba</h1>'
              . '<p>Cliente: Cliente Prueba</p>'
              . '<p>Socio: 0001</p>'
              . "<p>Periodo: {$periodo}</p>"
              . '<p>Este PDF es dummy — usado para validar el envío por WhatsApp Cloud API.</p>';

        $bytes = Pdf::loadHTML($html)->setPaper('a4')->output();
        Storage::disk('public')->put($pdfRelPath, $bytes);

        Resumen::updateOrCreate(
            ['cliente_id' => $cliente->id, 'periodo' => $periodo],
            [
                'pdf_path'   => $pdfRelPath,
                'estado'     => Resumen::PENDIENTE,
                'enviado_at' => null,
                'intentos'   => 0,
            ]
        );

        $this->command->info("Cliente Prueba + resumen pendiente creados. PDF: storage/app/public/{$pdfRelPath}");
    }
}
