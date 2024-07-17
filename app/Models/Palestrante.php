<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Palestrante extends Model
{
    use HasFactory;

    protected $table = 'palestrantes';

    protected $fillable = [
        'Nome',
        'Curriculo',
        'Foto'
    ];
}
