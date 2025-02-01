<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MezclaMaterial extends Model
{
    use HasFactory;
    
    protected $table = 'mezcla_materials';

    protected $fillable = [
        'mezcla_id',
        'codigo',
        'tipo_material',
        'insumo_id',
        'reprocesados_id',
        'tipo',
        'cantidad',
        'costo_kilo',
        'costo_total',
        'color',
    ];

    // Definir las relaciones con tipos explícitos
    public function mezcla(): BelongsTo
    {
        return $this->belongsTo(Mezclas::class, 'mezcla_id');
    }

    public function insumos(): BelongsTo
    {
        return $this->belongsTo(Insumos::class, 'insumo_id');
    }

    public function reprocesados(): BelongsTo
    {
        return $this->belongsTo(Reprocesados::class, 'reprocesados_id');
    }
    public function material()
    {
        return $this->morphTo();
    }
}
