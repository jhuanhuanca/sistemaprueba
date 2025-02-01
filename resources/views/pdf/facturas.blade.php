<!DOCTYPE html>
<html>
<head>
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h1, h2 {
            text-align: center;
            font-size: 16px;
        }
        .factura-container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #333;
            padding: 20px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo {
            text-align: left;
        }
        .fecha {
            text-align: right;
            font-size: 12px;
        }
        /* Nuevo estilo de firmas */
        .signatures {
            margin-top: 80px;
            text-align: center;
        }
        .signature-container {
            display: inline-block;
            margin: 0 50px;
        }
        .signature-line {
            width: 150px;
            border-top: 1px solid black;
            margin-bottom: 5px;
        }
        .signature-text {
            margin: 0;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo">
            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" width="100" height="50">
        </div>
        <div class="fecha">
            <p><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="factura-container">
        <h1>Factura</h1>
        <p><strong>Código de Venta:</strong> {{ $venta->codigo }}</p>
        <p><strong>Cliente:</strong> {{ $venta->Clientes->nombre }}</p>
        <p><strong>Fecha de Venta:</strong> {{ $venta->fecha_venta }}</p>
        <p><strong>Total:</strong> {{ number_format($venta->total, 2, '.', ',') }}<strong> Bs.</strong></p>

        <h2>Detalles de la Venta</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unidad</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->DetalleVentas as $detalle)
                <tr>
                    <td>{{ $detalle->productos->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ number_format($detalle->precio_unitario, 2, '.', ',') }}<strong> Bs.</strong></td>
                    <td>{{ number_format($detalle->cantidad * $detalle->precio_unitario, 2, '.', ',') }}<strong> Bs.</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>{{ number_format($venta->total, 2, '.', ',') }} Bs.</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="signatures">
            <div class="signature-container">
                <div class="signature-line"></div>
                <p class="signature-text">Firma de Recibido</p>
            </div>
            <div class="signature-container">
                <div class="signature-line"></div>
                <p class="signature-text">Firma del Vendedor</p>
            </div>
        </div>
    </div>
</body>
</html>
