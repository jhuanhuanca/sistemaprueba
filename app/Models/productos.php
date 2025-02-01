<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'almacen_id',
        'stock',
        'peso_unitario',
        'image',
        'stock_min',
        'stock_max',
        'costo_mercado',
        'estado',
    ];
    protected $casts = [
        'estado' => 'boolean',
    ];
    protected $attributes = [
        'stock' => 0,
        'peso_unitario' => 0,
    ];
    public function SubProducto()
    {
        return $this->hasMany(SubProducto::class,'producto_id');
    }
    public function categorias()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }
    public function OrdenProduccion()
    {
        return $this->hasMany(OrdenProduccion::class,'producto_id');
    }
    public function ControlCalidad()
    {
        return $this->hasMany(ControlCalidad::class,'producto_id');
    }
    public function DetalleVenta()
    {
        return $this->hasMany(DetalleVentas::class, 'producto_id');
    }
    public function ProduccionDiaria()
    {
        return $this->hasMany(ProduccionDiaria::class,'producto_id');
    }
    public function almacen()
    {
        return $this->belongsTo(Almacenes::class, 'almacen_id');
    }
    public function areas()
    {
        return $this->belongsTo(Areas::class, 'area_id');
    }
}
