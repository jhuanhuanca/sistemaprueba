<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masters extends Model
{
    use HasFactory;
    protected $fillable=[
        'codigo',
        'descripcion',
        'color',
        'peso',
        'precioLLegada',
        'precioPorGramo',
        'proveedor_id',
        'costoTransporte',
        'costoEnvio',
        'costofinal',
        'fecha',
    ];
    public function proveedores()
    {
        return $this->belongsTo(Proveedores::class,'proveedor_id');
    }
    public function mezclas()
    {
        return $this->hasMany(mezclas::class, 'master_id');
    }
}
