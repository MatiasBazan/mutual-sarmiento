<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'apellido',
        'nombre_completo',
        'celular',
        'direccion',
        'localidad',
        'nro_socio',
        'ultimo_periodo',
    ];

    public function resumenes(): HasMany
    {
        return $this->hasMany(Resumen::class);
    }
}
