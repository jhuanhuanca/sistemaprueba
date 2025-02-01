<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procesos extends Model
{
    use HasFactory;
    protected $fillable=[
        'codigo',
        'fabricacion_id',
        'asiento_id',
        'descripcion',
        'manoobra',
        'costoextra',
        'costoproceso',
    ];
    public function asientos()
    {
        return $this->belongsTo(asientos::class,'asiento_id');
    }
    public function fabricacion()
    {
        return $this->belongsTo(fabricacion::class,'fabricacion_id');
    }
}
