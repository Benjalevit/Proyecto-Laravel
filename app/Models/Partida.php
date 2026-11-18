<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = 'partidas';

    protected $fillable = [
        'user_id',
        'jugada_jugador',
        'jugada_ia',
        'resultado',
    ];
}
