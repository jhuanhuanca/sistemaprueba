<?php

namespace App\Models;

use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Filament\Facades\Filament;

class ProduccionDiaria extends Model
{
    use HasFactory, HasRoles;
    protected $fillable=[
        'fabricacion_id',
        'fecha',
        'hora_inicio',
        'hora_salida',
        'horas_trabajadas',
        'numero_lote', 
        'producto',
        'cantidad_producida',
        'color',
        'desperdicios',
        'equipo_id',
        'usuario',
        'observaciones',
        'registrado', 

    ];
    
    public function Fabricacion()
    {
        return $this->belongsTo(Fabricacion::class,'fabricacion_id');
    }
    public function equipos()
    {
        return $this->belongsTo(equipos::class,'equipo_id');
    }
    public function empleados()
    {
        return $this->belongsTo(empleados::class,'operario_id');
    }
    public function ControlProduccion()
    {
        return $this->hasMany(ControlProduccion::class,'produccion_diaria_id');
    }
    public function produccionfinal()
    {
        return $this->hasMany(ProduccionFinal::class,'produccion_diaria_id');
    }
    //para ver solo registros del usuario autentificado
    protected static function booted()
    {
        static::addGlobalScope('user', function ($builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if (!$user->roles->contains('name', 'super-Admin')) {
                    $builder->where('usuario', $user->name);
                }
            }
        });
    }
    
}
