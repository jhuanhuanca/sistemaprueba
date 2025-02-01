<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class areas extends Model
{
    use HasFactory;
    protected $fillable=[
    'descripcion',
    ];



    public function categorias()
    {
        return $this->hasMany(categorias::class,'area_id');
    }
    public function OrdenProduccion()
    {
        return $this->hasMany(OrdenProduccion::class,'area_id');
    }
    public function equipos()
    {
        return $this->hasMany(equipos::class,'area_id');
    }
}

