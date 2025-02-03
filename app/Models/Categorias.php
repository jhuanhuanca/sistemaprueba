<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    use HasFactory;
    protected $fillable=[
        'descripcion',
        'area_id',
    ];

    public function areas()
    {
        return $this->belongsTo(areas::class,'area_id');
    }
    public function productos()
    {
        return $this->hasMany(productos::class,'categoria_id');
    }
}
