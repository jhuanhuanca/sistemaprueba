<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reprocesados extends Model
{
    use HasFactory;
    protected $fillable=[
        'codigo',
        'descripcion',
        'peso',
        'color',
        'estado',
        'fecha',
        'hora_inicio',
        'hora_salida',
        'horas_trabajadas',
        'equipo_id',
        'costoMaq',
        'empleado_id',
        'costoEmp',
        'costoManoObra',
        'costokilo',
        'costoextra',
        'otroscostos',
        'costoTotal'
    ];
    public function equipos()
    {
        return $this->belongsTo(Equipos::class, 'equipo_id');
    }
    public function empleados()
    {
        return $this->belongsTo(Empleados::class, 'empleado_id');
    }
}
