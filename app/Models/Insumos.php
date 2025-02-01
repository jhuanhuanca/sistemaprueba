<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumos extends Model
{
    use HasFactory;

    protected $fillable = [
    'codigo',
    'nombre',
    'descripcion',
    'almacen_id',
    'proveedor_id',
    'area_id',
    'tipo',
    'fardos',
    'pesoxfardo',
    'stock',
    'unidad',
    'color',
    'stock_min',
    'stock_max',
    'costoLLegada',
    'costo_kilo',
    'costoTransporte',
    'costoEnvio',
    'impuetoInportacion',
    'costototal',
    'image',
    ];

    // Relación con el proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class,'proveedor_id');
    }
    public function MezclaMaterial()
    {
        return $this->hasMany(MezclaMaterial::class,'insumo_id');
    }
    public function area()
    {
        return $this->belongsTo(Areas::class,'area_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacenes::class,'almacen_id');
    }

}
