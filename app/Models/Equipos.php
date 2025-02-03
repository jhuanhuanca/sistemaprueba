<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipos extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'area_id',
        'marca',
        'modelo',
        'proveedor_id',
        'estado',
        'factorPotencia',
        'voltaje',
        'amperaje',
        'consumoEnergetico',
        'depreciacionAnual',
        'costoMantenimiento',
        'costoMaq',
        'image',
    ];

    // Relación con el proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class,'proveedor_id');
    }
    public function ProduccionDiaria()
    {
        return $this->hasMany(ProduccionDiaria::class,'equipo_id');
    }
    public function fabricacion()
    {
        return $this->hasMany(fabricacion::class,'equipo_id');
    }
    public function areas()
    {
        return $this->belongsTo(areas::class,'area_id');
    }
    public function reprocesados()
    {
        return $this->hasMany(Reprocesados::class,'equipo_id');
    }
    public function registrosMezcla()
    {
        return $this->hasMany(RegistrosMezcla::class, 'equipo_id');
    }
}
