<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submissao extends Model
{
    use HasFactory;

    protected $table = 'submissoes';

    protected $fillable = [
        'IDEvento',
        'IDAvaliador',
        'Titulo',
        'Regras'
    ];
}
