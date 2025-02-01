<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'rut_cif_nit',
        'ciudad',
        'codigo_postal',
        'pais',
        'telefono',
        'email',
        'sitio_web',
        'fecha_registro',
        'estado',
        'notas',
    ];

    // Relación con insumos
    public function insumos()
    {
        return $this->hasMany(Insumos::class,'proveedor_id');
    }
    public function equipos()
    {
        return $this->hasMany(Equipos::class,'proveedor_id');
    }
    public function master()
    {
        return $this->hasMany(Masters::class,'proveedor_id');
    }
}


