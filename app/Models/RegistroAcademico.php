<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroAcademico extends Model
{
    protected $table = 'registros_academicos';

    protected $fillable = [
        'user_id',
        'nombre_alumno',
        'faltas',
        'calificacion',
        'equivalencia',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcula la equivalencia alfabética de Universidad Tecnológica a partir de la calificación (0 a 10).
     */
    public static function calcularEquivalencia(float $calificacion): string
    {
        if ($calificacion < 8.0) {
            return 'NA';
        }

        if ($calificacion < 9.0) {
            return 'SA';
        }

        if ($calificacion < 9.5) {
            return 'DE';
        }

        return 'AU';
    }
}
