<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResumenProgreso implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int    $resumenId,
        public string $estado,
        public string $clienteNombre,
        public int    $total,
        public int    $procesados,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('resumenes');
    }

    public function broadcastAs(): string
    {
        return 'progreso.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'resumen_id'      => $this->resumenId,
            'estado'          => $this->estado,
            'cliente_nombre'  => $this->clienteNombre,
            'total'           => $this->total,
            'procesados'      => $this->procesados,
            'porcentaje'      => $this->total > 0 ? round(($this->procesados / $this->total) * 100) : 0,
        ];
    }
}
