<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrosMezcla extends Model
{
    use HasFactory;
    protected $table = 'registros_mezcla';
    protected $fillable = [
        'codigo', 
        'fabricacion_id', 
        'mezcla_id', 
        'empleado_id', 
        'equipo_id', 
        'cantidad_por_mezclar',
        'fecha',
        'hora_inicio', 
        'hora_fin',
        'horas_trabajadas', 
        'mano_obra', 
        'costo_maquina', 
        'costo_total', 
        'cantidad_mezcladas',
        'observaciones'
        ];

    public function fabricacion()
    {
        return $this->belongsTo(Fabricacion::class, 'fabricacion_id');
    }

    public function mezcla()
    {
        return $this->belongsTo(Mezclas::class, 'mezcla_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'empleado_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'equipo_id');
    }   
}
