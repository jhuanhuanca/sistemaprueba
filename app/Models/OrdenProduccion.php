<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    use HasFactory;
    protected $fillable=[
        'codigo',
        'descripcion',
        'area_id',
        'fecha_creacion',
        'producto_id',
        'cantidad_producir',
        'color',
        'tipo_material',
        'usuario',
        'cliente_id',
        'fecha_finalizacion_estimada',
        'fecha_entrega',
        'estado',
        'observaciones',
        'costo_estimado',
    ];
    public function productos()
    {
        return $this->belongsTo(productos::class,'producto_id');
    }
    public function areas()
    {
        return $this->belongsTo(areas::class,'area_id');
    }
    
    public function clientes()
    {
        return $this->belongsTo(clientes::class,'cliente_id');
    }
    public function fabricacion()
    {
        return $this->hasMany(fabricacion::class,'orden_produccion_id');
    }
    public function entregas()
    {
        return $this->hasMany(Entregas::class,'orden_produccion_id');
    }
}
