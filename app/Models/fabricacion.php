<?php

namespace App\Models;

use App\Http\Controllers\CalculoMezclasController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fabricacion extends Model
{
    use HasFactory;
    protected $fillable=[
        'codigo',
        'orden_produccion_id',
        'area',
        'producto',
        'equipo_id', 
        'costo_maq',
        'mezcla_id',
        'tipo_material',
        'cantidad_a_producir',
        'cantidad_mezclas', 
        'costo_mezcla',
        'empleado_id',
        'usuario',
        'fecha_inicio',
        'fecha_finalizacion',
        'estado',
        'costo_total',
    ];
    protected $casts = [
        'fecha_finalizacion' => 'datetime',
        'estado' => 'boolean',
    ];
    public function OrdenProduccion()
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }
    public function empleados()
    {
        return $this->belongsTo(empleados::class, 'empleado_id');
    }
    public function equipos()
    {
        return $this->belongsTo(Equipos::class, 'equipo_id');
    }
    public function mezcla()
    {
        return $this->belongsTo(Mezclas::class, 'mezcla_id');
    }
    public function ProduccionDiariaa()
    {
        return $this->hasMany(ProduccionDiaria::class,'fabricacion_id');
        
    }
    public function Procesos()
    {
        return $this->hasMany(Procesos::class,'fabricacion_id');
    }
    public function produccionfinal()
    {
        return $this->hasMany(ProduccionFinal::class,'fabricacion_id');
    }
    public function registrosMezcla()
    {
        return $this->hasMany(RegistrosMezcla::class, 'fabricacion_id');
    }
}
