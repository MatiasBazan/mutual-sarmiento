<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resumen extends Model
{
    use HasFactory;
    protected $table = 'resumenes';

    protected $fillable = [
        'cliente_id',
        'periodo',
        'pdf_path',
        'estado',
        'enviado_at',
        'intentos',
    ];

    protected function casts(): array
    {
        return [
            'enviado_at' => 'datetime',
        ];
    }

    // Estados
    const PENDIENTE  = 'pendiente';
    const ENVIANDO   = 'enviando';
    const NOTIFICADO = 'notificado';
    const ERROR      = 'error';

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', self::PENDIENTE);
    }

    public function scopeConError($query)
    {
        return $query->where('estado', self::ERROR);
    }

    public function scopePeriodo($query, string $periodo)
    {
        return $query->where('periodo', $periodo);
    }
}