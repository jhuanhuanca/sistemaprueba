<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Mezclas;
use Illuminate\Http\Request;

class CalculoMezclasController extends Controller
{
    public function calcularCantidadMezclas($cantidadProduccion, $productoId, $mezclaId)
    {
        $pesoProducto = 0;
        $kilosMezcla = 0;
    
        // Obtener peso del producto
        if ($productoId) {
            $producto = Productos::find($productoId);
            if ($producto) {
                $pesoProducto = floatval($producto->peso_unitario ?? 0);
            }
        }
    
        // Obtener kilos de mezcla
        if ($mezclaId) {
            $mezcla = Mezclas::find($mezclaId);
            $kilosMezcla = floatval($mezcla->kilos_utilizados ?? 0);
        }
    
        // Calcular cantidad de mezclas necesarias
        if ($pesoProducto > 0 && $kilosMezcla > 0) {
            $pesoTotalRequerido = (int)($cantidadProduccion * $pesoProducto);
            return (int)ceil($pesoTotalRequerido / $kilosMezcla);
        }
    
        return 0;
    }
} 