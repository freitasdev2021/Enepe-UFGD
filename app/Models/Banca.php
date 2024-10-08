<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banca extends Model
{
    use HasFactory;

    protected $table = "bancaevento";

    protected $fillable = [
        "Tipo",
        "IDEvento",
        "IDUser"
    ];
}
