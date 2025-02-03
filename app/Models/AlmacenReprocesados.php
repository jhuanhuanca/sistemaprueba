<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlmacenReprocesados extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo',
        'descripcion',
        'peso',
        'color',
        'estado',
        'fecha',
        'costokilo'
    ];
    

}
