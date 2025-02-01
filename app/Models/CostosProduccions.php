<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostosProduccions extends Model
{
    use HasFactory;
    protected $fillable = [
        'produccion_total_id',
        'codigo_fabricacion',
        'producto',
        'cantidad',
        'costoProduccion',
        'costoUnidad'
    ];  
    public function ProduccionTotal()
    {
        return $this->belongsTo(ProduccionTotal::class, 'produccion_total_id');
    }
}
