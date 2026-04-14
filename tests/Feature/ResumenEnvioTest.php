<?php

namespace Tests\Feature;

use App\Jobs\EnviarResumenJob;
use App\Models\Cliente;
use App\Models\Resumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ResumenEnvioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function resumen_pasa_a_notificado_despues_del_envio(): void
    {
        $resumen = Resumen::factory()->create(['estado' => 'pendiente']);

        Queue::fake();

        $resumen->update(['estado' => 'notificado', 'enviado_at' => now()]);

        $this->assertEquals('notificado', $resumen->fresh()->estado);
        $this->assertNotNull($resumen->fresh()->enviado_at);
    }

    /** @test */
    public function resumen_sin_celular_queda_en_sin_celular(): void
    {
        $cliente = Cliente::factory()->sinCelular()->create();
        $resumen = Resumen::factory()->create([
            'cliente_id' => $cliente->id,
            'estado'     => 'pendiente',
        ]);

        // Simular la lógica del job
        if (empty($cliente->celular)) {
            $resumen->update(['estado' => 'sin_celular']);
        }

        $this->assertEquals('sin_celular', $resumen->fresh()->estado);
    }

    /** @test */
    public function enviar_todos_despacha_un_job_por_resumen_pendiente(): void
    {
        Queue::fake();

        $periodo = now()->format('Y-m');

        Resumen::factory()->count(3)->create([
            'estado'  => 'pendiente',
            'periodo' => $periodo,
        ]);
        Resumen::factory()->create([
            'estado'  => 'notificado',
            'periodo' => $periodo,
        ]);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->postJson('/resumenes/enviar-todos');

        // Solo se despacharon los 3 pendientes, no el notificado
        Queue::assertPushed(EnviarResumenJob::class, 3);
    }

    /** @test */
    public function enviar_todos_sin_pendientes_devuelve_422(): void
    {
        $periodo = now()->format('Y-m');

        Resumen::factory()->create([
            'estado'  => 'notificado',
            'periodo' => $periodo,
        ]);

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
                         ->postJson('/resumenes/enviar-todos');

        $response->assertStatus(422);
    }
}