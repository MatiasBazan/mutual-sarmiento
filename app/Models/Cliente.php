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

    public function tieneCelular(): bool
    {
        return !empty($this->celular);
    }

    /**
     * Normaliza el celular al formato WhatsApp: whatsapp:+5493XXXXXXXXXX
     *
     * Casos manejados:
     *   3472548492   → whatsapp:+5493472548492  (10 dígitos)
     *   93472548492  → whatsapp:+5493472548492  (11 dígitos con 9)
     *   03472548492  → whatsapp:+5493472548492  (con 0 inicial)
     *   153472548492 → whatsapp:+5493472548492  (con 15 inicial)
     *   5493472548492 → whatsapp:+5493472548492 (ya completo)
     */
    public static function normalizarCelular(string $celular): string
    {
        // Quitar todo lo que no sea dígito
        $digits = preg_replace('/\D/', '', $celular);

        // Si ya tiene código de país (54) al inicio
        if (str_starts_with($digits, '54')) {
            // Asegurar que tenga el 9 de celular: +549...
            if (!str_starts_with($digits, '549')) {
                $digits = '549' . substr($digits, 2);
            }
            return 'whatsapp:+' . $digits;
        }

        // Si empieza con 0 → quitarlo
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Si empieza con 15 → quitarlo (prefijo de celular viejo)
        if (str_starts_with($digits, '15')) {
            $digits = substr($digits, 2);
        }

        // 11 dígitos empezando con 9 → ya tiene el prefijo móvil
        if (strlen($digits) === 11 && str_starts_with($digits, '9')) {
            return 'whatsapp:+54' . $digits;
        }

        // 10 dígitos (área + número) → agregar +54 9
        return 'whatsapp:+549' . $digits;
    }
}
