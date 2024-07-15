<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Palestra extends Model
{
    use HasFactory;

    protected $table = 'palestra';

    protected $fillable = [
        'Titulo',
        'Palestra',
        'Data',
        'Inicio',
        'Termino'
    ];
}
