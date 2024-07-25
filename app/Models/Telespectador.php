<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telespectador extends Model
{
    use HasFactory;

    protected $table = 'telespectadores';

    protected $fillable = [
        'IDInscrito',
        'IDPalestra'
    ];
}
