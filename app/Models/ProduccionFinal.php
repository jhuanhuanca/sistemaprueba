<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduccionFinal extends Model
{
    use HasFactory;
    protected $fillable=[
        'fabricacion_id',
        'produccion_diaria_id',
        'cantidad',
        'progreso',
        'producto',
    ];
   
    public function fabricacion()
    {
        return $this->belongsTo(fabricacion::class,'fabricacion_id');
    }
    public function producciondiaria()
    {
        return $this->belongsTo(ProduccionDiaria::class,'produccion_diaria_id');
    }

    protected static function booted()
    {
        static::created(function ($produccionFinal) {
            static::actualizarTotales($produccionFinal);
        });

        static::updated(function ($produccionFinal) {
            static::actualizarTotales($produccionFinal);
        });

        static::deleted(function ($produccionFinal) {
            static::actualizarTotales($produccionFinal);
        });
    }

    protected static function actualizarTotales($produccionFinal)
    {
        // Calcular el total de la producción
        $total = ProduccionFinal::where('fabricacion_id', $produccionFinal->fabricacion_id)
            ->where('producto', $produccionFinal->producto)
            ->sum('cantidad');

        // Calcular el total entregado
        $entregado = Entregas::join('fabricacions', 'entregas.orden_produccion_id', '=', 'fabricacions.orden_produccion_id')
            ->where('fabricacions.id', $produccionFinal->fabricacion_id)
            ->where('entregas.producto', $produccionFinal->producto)
            ->sum('entregado');

        // Actualizar o crear el registro de totales
        ProduccionTotal::updateOrCreate(
            [
                'fabricacion_id' => $produccionFinal->fabricacion_id,
                'producto' => $produccionFinal->producto,
            ],
            [
                'total_cantidad' => $total,
                'cantidad_entregada' => $entregado,
                'cantidad_disponible' => $total - $entregado,
            ]
        );
    }
}
