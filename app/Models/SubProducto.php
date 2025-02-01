<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubProducto extends Model
{
    use HasFactory;
    protected $fillable =[
        'producto_id',
        'nombre',
        'color',
        'tipo',
        'cantidad',
        'disponible',
        'unidxpaq',
        'paq',
        'peso',
    ];
    public function productos()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }
}
