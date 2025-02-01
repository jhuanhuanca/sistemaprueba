<?php

namespace App\Models;

use App\Filament\Resources\OrdenProduccionResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Entregas extends Model
{
    protected $fillable = [
        'codigo',
        'fecha',
        'entregado',
        'faltante',
        'total',
        'usuario',
        'orden_produccion_id',
        'producto',
        'color',
        'observaciones'
    ];

    protected static function booted()
    {
        static::creating(function ($entrega) {
            $entrega->usuario = Auth::user()->name;
            if ($entrega->orden_produccion_id) {
                $ordenProduccion = OrdenProduccion::find($entrega->orden_produccion_id);
                $entrega->producto = $entrega->producto ?? $ordenProduccion->productos->nombre ?? 'N/A';
                $entrega->total = $entrega->total ?? $ordenProduccion->cantidad_producir ?? 0;
            }
        });

        static::created(function ($entrega) {
            $cantidadPorEntregar = $entrega->entregado;

            // Obtener todas las producciones finales relacionadas ordenadas por ID
            $produccionesFinales = ProduccionFinal::query()
                ->join('fabricacions', 'produccion_finals.fabricacion_id', '=', 'fabricacions.id')
                ->where('fabricacions.orden_produccion_id', $entrega->orden_produccion_id)
                ->where('produccion_finals.producto', $entrega->producto)
                ->orderBy('produccion_finals.id', 'asc')
                ->get();

            // Restar secuencialmente de cada producción final
            foreach ($produccionesFinales as $produccionFinal) {
                if ($cantidadPorEntregar <= 0) break;

                $cantidadDisponible = $produccionFinal->cantidad;
                $cantidadARestar = min($cantidadDisponible, $cantidadPorEntregar);

                // Actualizar la cantidad en la producción final
                $produccionFinal->cantidad = $cantidadDisponible - $cantidadARestar;
                $produccionFinal->save();

                // Actualizar la cantidad restante por entregar
                $cantidadPorEntregar -= $cantidadARestar;
            }

            // Actualizar la orden de producción
            $ordenProduccion = $entrega->ordenproduccion;
            if ($ordenProduccion) {
                $ordenProduccion->cantidad_producir = $entrega->faltante;
                $ordenProduccion->save();
            }
        });

        static::updated(function ($entrega) {
            $diferencia = $entrega->entregado - $entrega->getOriginal('entregado');
            $cantidadPorEntregar = $diferencia;

            // Obtener todas las producciones finales relacionadas ordenadas por ID
            $produccionesFinales = ProduccionFinal::query()
                ->join('fabricacions', 'produccion_finals.fabricacion_id', '=', 'fabricacions.id')
                ->where('fabricacions.orden_produccion_id', $entrega->orden_produccion_id)
                ->where('produccion_finals.producto', $entrega->producto)
                ->orderBy('produccion_finals.id', 'asc')
                ->get();

            // Si es una reducción de entrega, aumentar las cantidades
            if ($diferencia < 0) {
                $cantidadPorDevolver = abs($diferencia);
                foreach ($produccionesFinales as $produccionFinal) {
                    if ($cantidadPorDevolver <= 0) break;
                    $produccionFinal->cantidad += $cantidadPorDevolver;
                    $produccionFinal->save();
                    $cantidadPorDevolver = 0;
                }
            } else {
                // Si es un aumento de entrega, restar secuencialmente
                foreach ($produccionesFinales as $produccionFinal) {
                    if ($cantidadPorEntregar <= 0) break;

                    $cantidadDisponible = $produccionFinal->cantidad;
                    $cantidadARestar = min($cantidadDisponible, $cantidadPorEntregar);

                    // Actualizar la cantidad en la producción final
                    $produccionFinal->cantidad = $cantidadDisponible - $cantidadARestar;
                    $produccionFinal->save();

                    // Actualizar la cantidad restante por entregar
                    $cantidadPorEntregar -= $cantidadARestar;
                }
            }

            // Actualizar la orden de producción
            $ordenProduccion = $entrega->ordenproduccion;
            if ($ordenProduccion) {
                $ordenProduccion->cantidad_producir = $entrega->faltante;
                $ordenProduccion->save();
            }
        });
    }

    public function ordenproduccion()
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }
}
