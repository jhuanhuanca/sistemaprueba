<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Services\PdfService;

class PedidoController
{
    public function generarCotizacion(Pedido $pedido)
    {
        $pdfService = new PdfService();
        return $pdfService->generarCotizacion($pedido);
    }

    public function generarPedido(Pedido $pedido)
    {
        $pdfService = new PdfService();
        return $pdfService->generarPedido($pedido);
    }
} 