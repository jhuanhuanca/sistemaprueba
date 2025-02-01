<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentas extends Model
{
    use HasFactory;
    protected $fillable=[
            'codigo',
            'venta_id',
            'producto_id',
            'almacen_id',
            'subproducto_id',
            'cantidad',
            'precio_unitario',
            'subtotal', 
    ];
    public function ventas()
    {
        return $this->belongsTo(ventas::class,'venta_id');
    }
    public function productos()
    {
        return $this->belongsTo(productos::class,'producto_id');
    }
    public function almacenes()
    {
        return $this->belongsTo(almacenes::class,'almacen_id');
    }   
}
