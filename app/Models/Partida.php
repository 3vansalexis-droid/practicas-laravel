<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partida extends Model
{
    protected $table = 'partidas';

    protected $fillable = [
        'jugador_x_id',
        'jugador_o_id',
        'turno_actual',
        'tablero',
        'estado',
        'ganador_id',
        'es_empate',
    ];

    protected $casts = [
        'tablero' => 'array',
        'es_empate' => 'boolean',
    ];

    public function jugadorX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_x_id');
    }

    public function jugadorO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_o_id');
    }

    public function ganador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ganador_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    /**
     * Verifica las 8 combinaciones ganadoras del tablero 3x3.
     * Retorna 'X', 'O' o null si todavía no hay ganador.
     */
    public static function detectarGanador(array $tablero): ?string
    {
        $combinaciones = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6],
        ];

        foreach ($combinaciones as [$a, $b, $c]) {
            if (
                isset($tablero[$a]) &&
                $tablero[$a] !== null &&
                $tablero[$a] === ($tablero[$b] ?? null) &&
                $tablero[$a] === ($tablero[$c] ?? null)
            ) {
                return $tablero[$a];
            }
        }

        return null;
    }
}
