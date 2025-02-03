<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduccionTotal extends Model
{
    protected $table = 'produccion_totales';
    
    protected $fillable = [
        'fabricacion_id',
        'producto',
        'costoproceso',
        'costoMaq',
        'costoManoObra',
        'costoTotal',
        'total_cantidad',
        'cantidad_entregada',
        'cantidad_disponible'
    ];

    public function fabricacion()
    {
        return $this->belongsTo(Fabricacion::class);
    }
   
} 