<?php

namespace App\Http\Controllers;
use App\Models\Insumos;
use Illuminate\Http\Request;
abstract class Controller
{
    // 
    public function index()
    {
        // Usa 'with' para cargar la relación 'proveedor'
        $insumos = Insumos::with('proveedor')->get();
        return view('insumos.index', compact('insumos'));
    }
}
