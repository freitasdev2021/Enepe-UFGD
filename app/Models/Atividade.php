<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $table = 'atividades';

    protected $fillable = [
        'IDSala',
        'Titulo',
        'Descricao',
        'Inicio',
        'Chat',
        'URLMeeting',
        'IDMeeting'
    ];
}
