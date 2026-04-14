<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Resumen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resumen>
 */
class ResumenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'periodo'    => now()->format('Y-m'),
            'pdf_path'   => 'resumenes/' . now()->format('Y-m') . '/resumen_test.pdf',
            'estado'     => Resumen::PENDIENTE,
            'enviado_at' => null,
            'intentos'   => 0,
        ];
    }

    public function pendiente(): static
    {
        return $this->state(['estado' => Resumen::PENDIENTE]);
    }

    public function notificado(): static
    {
        return $this->state([
            'estado'     => Resumen::NOTIFICADO,
            'enviado_at' => now(),
        ]);
    }

    public function conError(): static
    {
        return $this->state(['estado' => Resumen::ERROR]);
    }

    public function sinCelular(): static
    {
        return $this->state(['estado' => Resumen::SIN_CELULAR]);
    }
}