<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlCalidad extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'producto_id',
        'usuario',
        'punto_inspeccion',
        'resultado',
        'defectos_encontrados',
        'mediciones',
        'accion_correctiva',
        'observaciones',
    ];
    public function productos()
    {
        return $this->belongsTo(productos::class,'producto_id');
    }
    
}
