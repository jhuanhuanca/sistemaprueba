<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $fillable = [
    'NitCi',
    'nombre',
    'ciudad',
    'pais',
    'telefono',
    'email',
    'fecha_registro',
    'estado',
    'notas',
     
    ];
    public function ventas()
    {
        return $this->hasMany(ventas::class,'cliente_id');
    }
    public function OrdenProduccion()
    {
        return $this->hasMany(OrdenProduccion::class,'cliente_id');
    }
}
