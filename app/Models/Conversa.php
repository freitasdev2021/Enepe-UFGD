<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversa extends Model
{
    use HasFactory;

    protected $table = 'conversas';

    protected $fillable = [
        'IDRemetente',
        'IDDestinatario',
        'Titulo'
    ];
}
