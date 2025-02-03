<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlProduccion extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'fecha',
        'produccion_diaria_id',
        'nombre_operario',
        'usuario',
        'produccion_aceptada',
        'produccion_rechazada',
        'resultado',
        'defectos_encontrados',
        'observaciones',
    ];
        
    public function ProduccionDiaria()
    {
        return $this->belongsTo(ProduccionDiaria::class, 'produccion_diaria_id');
    }
}
