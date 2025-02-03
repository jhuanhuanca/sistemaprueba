<?php

namespace App\Services;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generarCotizacion(Pedido $pedido)
    {
        $pdf = PDF::loadView('pdfs.cotizacion', [
            'pedido' => $pedido,
            'fecha' => now()->format('d \d\e F \d\e Y'),
            'codigo' => 'CITE: ' . $pedido->codigo,
        ]);

        return $pdf->stream('cotizacion-' . $pedido->codigo . '.pdf');
    }

    public function generarPedido(Pedido $pedido)
    {
        $pdf = PDF::loadView('pdfs.pedido', [
            'pedido' => $pedido,
            'fecha' => now()->format('d-m-Y'),
            'hora' => now()->format('H:i:s'),
        ]);

        return $pdf->stream('pedido-' . $pedido->codigo . '.pdf');
    }
}
