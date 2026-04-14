<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        $apellido = strtoupper(fake()->lastName());

        return [
            'apellido'        => $apellido,
            'nombre_completo' => $apellido . ' ' . strtoupper(fake()->firstName()),
            'celular'         => fake()->numerify('34725#####'),
            'direccion'       => strtoupper(fake()->streetName()) . ' ' . fake()->buildingNumber(),
            'nro_socio'       => '0001-' . fake()->unique()->numerify('########'),
            'ultimo_periodo'  => now()->format('Y-m'),
        ];
    }

    public function sinCelular(): static
    {
        return $this->state(['celular' => null]);
    }
}