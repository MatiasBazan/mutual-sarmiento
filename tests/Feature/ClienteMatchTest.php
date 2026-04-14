<?php

namespace Tests\Feature;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteMatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function crea_cliente_nuevo_si_no_existe(): void
    {
        $this->assertDatabaseCount('clientes', 0);

        Cliente::updateOrCreate(
            ['nro_socio' => '0001-00026978'],
            ['apellido' => 'GOMEZ', 'nombre_completo' => 'GOMEZ GUSTAVO ESTEBAN']
        );

        $this->assertDatabaseCount('clientes', 1);
        $this->assertDatabaseHas('clientes', ['nro_socio' => '0001-00026978']);
    }

    /** @test */
    public function actualiza_cliente_existente_sin_duplicar(): void
    {
        Cliente::factory()->create(['nro_socio' => '0001-00026978']);

        Cliente::updateOrCreate(
            ['nro_socio' => '0001-00026978'],
            ['nombre_completo' => 'GOMEZ GUSTAVO ESTEBAN ACTUALIZADO']
        );

        $this->assertDatabaseCount('clientes', 1);
        $this->assertDatabaseHas('clientes', [
            'nombre_completo' => 'GOMEZ GUSTAVO ESTEBAN ACTUALIZADO',
        ]);
    }

    /** @test */
    public function dos_clientes_con_mismo_apellido_no_se_mezclan(): void
    {
        Cliente::factory()->create([
            'apellido'  => 'GONZALEZ',
            'nro_socio' => '0001-00011111',
        ]);

        Cliente::updateOrCreate(
            ['nro_socio' => '0001-00022222'],
            ['apellido'  => 'GONZALEZ', 'nombre_completo' => 'GONZALEZ MARIA']
        );

        $this->assertDatabaseCount('clientes', 2);
    }

    /** @test */
    public function busca_por_apellido_cuando_no_hay_nro_socio(): void
    {
        Cliente::factory()->create(['apellido' => 'PEREZ', 'nro_socio' => null]);

        Cliente::updateOrCreate(
            ['apellido' => 'PEREZ'],
            ['nombre_completo' => 'PEREZ JUAN CARLOS']
        );

        $this->assertDatabaseCount('clientes', 1);
        $this->assertDatabaseHas('clientes', ['nombre_completo' => 'PEREZ JUAN CARLOS']);
    }
}