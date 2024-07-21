<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reprovacao extends Model
{
    use HasFactory;

    protected $table = 'reprovacoes';

    protected $fillable = [
        'IDEntrega',
        'Feedback',
        'Status'
    ];
}
