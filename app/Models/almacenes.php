<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class almacenes extends Model
{
    use HasFactory;

    // Especificar el nombre exacto de la tabla
    protected $table = 'almacenes';

        protected $fillable = [
            'codigo',
        'descripcion', 
        
    ];

    public function insumos()
    {
        return $this->hasMany(insumos::class);
    }

    public function productos()
    {
        return $this->hasMany(productos::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function ventas()
    {
        return $this->hasMany(ventas::class,'almacen_id');
    }

    public function subproductos()
    {
        return $this->hasMany(SubProducto::class);
    }

    public function detalle_ventas()
    {
        return $this->hasMany(DetalleVentas::class);
    }

}
