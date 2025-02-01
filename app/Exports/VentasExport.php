<?php

namespace App\Exports;

use App\Models\ventas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ventas::with('cliente') // Asegúrate de usar 'cliente' en singular
            ->get()
            ->map(function ($venta) {
                return [
                    'codigo' => $venta->codigo,
                    'cliente' => optional($venta->cliente)->nombre, // Usa 'optional' para evitar errores si cliente es nulo
                    'fecha_venta' => $venta->fecha_venta,
                    'usuario' => $venta->usuario,
                    'total' => $venta->total,
                    'metodo_pago' => $venta->metodo_pago,
                    'estado' => $venta->estado,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Cliente',
            'Fecha de Venta',
            'Usuario',
            'Total',
            'Método de Pago',
            'Estado',
        ];
    }
}
