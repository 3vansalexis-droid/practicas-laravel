<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservacion extends Model
{
    protected $table = 'reservaciones';

    protected $fillable = [
        'user_id',
        'equipo_id',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_reserva' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }
}
