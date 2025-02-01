<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class empleados extends Model
{
    use HasFactory;
    protected $fillable=[
            'codigo',
            'ci',
            'nombres',
            'apellidos',
            'telefono',
            'cargo',
            'salario',
            'salario_hora',
    ];
    public function empleados()
    {
        return $this->hasMany(empleados::class,'empleado_id');
    }
    public function ProduccionDiaria()
    {
        return $this->hasMany(ProduccionDiaria::class,'empleado_id');
    }
    public function reprocesados()
    {
        return $this->hasMany(Reprocesados::class,'empleado_id');
    }
    public function registrosMezcla()
    {
        return $this->hasMany(RegistrosMezcla::class, 'empleado_id');
    }

    
            
}
