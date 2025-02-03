<?php

namespace App\Http\Controllers;
use App\Models\Mezclas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MezclasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        //
    }
    public function calculateKilosUtilizados($mezclaMaterial)
    {    
        $mezcla = Mezclas::with('mezclaMaterial')->findOrFail($mezclaMaterial);
        $mezcla->load('mezclaMaterial');
        
        // Calcular el total de kilos
        $totalKilos = $mezcla->mezclaMaterial->sum('cantidad');
        $mezcla->kilos_utilizados = $totalKilos;
        $mezcla->save();
        
        return $totalKilos;
    }
    public function calculateCostos($mezclaMaterial)
    {    
        $mezcla = Mezclas::with('mezclaMaterial')->findOrFail($mezclaMaterial);
        $mezcla->load('mezclaMaterial');
        
        // Calcular el total de kilos
        $totalCostos = $mezcla->mezclaMaterial->sum('costo_total');
        $mezcla->costo_mezcla = $totalCostos;
        $mezcla->save();
        $mezcla->refresh();
        return $totalCostos;
    }
    public function calculatePorcentajes($mezclaMaterial)
    {    
        $mezcla = Mezclas::with(['mezclaMaterial'])->findOrFail($mezclaMaterial);
        
        $totalKilos = $mezcla->kilos_utilizados ?: 0;
        
        if ($totalKilos > 0) {
            // Calcular kilos por tipo de material
            $kilosReciclado = $mezcla->mezclaMaterial
                ->filter(function ($item) {
                    return $item->tipo === 'material reciclado';
                })
                ->sum('cantidad');
            
            $kilosVirgen = $mezcla->mezclaMaterial
                ->filter(function ($item) {
                    return $item->tipo === 'material virgen';
                })
                ->sum('cantidad');
            
            // Calcular porcentajes
            $porcentajeReciclado = ($kilosReciclado / $totalKilos) * 100;
            $porcentajeVirgen = ($kilosVirgen / $totalKilos) * 100;
            
            // Actualizar los campos
            $mezcla->reciclado = round($porcentajeReciclado, 2);
            $mezcla->virgen = round($porcentajeVirgen, 2);
            $mezcla->save();
            
            // Forzar la actualización
            $mezcla->refresh();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
