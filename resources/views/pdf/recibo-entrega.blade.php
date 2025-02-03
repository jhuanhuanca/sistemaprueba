<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Entrega</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
        }
        .details {
            margin-bottom: 20px;
        }
        .details p {
            margin: 3px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
        }
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
    <div class="header">
    <div class="logo">
            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" width="100" height="50">
        </div>
        <h1>Recibo de Entrega</h1>
    </div>

    <div class="details">
        <p><strong>Código:</strong> {{ $entrega->codigo }}</p>
        <p><strong>Fecha:</strong> {{ $entrega->fecha }}</p>
        <p><strong>Encargado:</strong> {{ $entrega->usuario }}</p>
        <p><strong>Orden de Producción:</strong> {{ $entrega->ordenproduccion->codigo }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Color</th>
                <th>Cantidad Total</th>
                <th>Cantidad Entregada</th>
                <th>Faltante</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $entrega->producto }}</td>
                <td>{{ $entrega->color }}</td>
                <td>{{ number_format($entrega->total, 2) }}</td>
                <td>{{ number_format($entrega->entregado, 2) }}</td>
                <td>{{ number_format($entrega->faltante, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Observaciones:</strong></p>
    </div>

    <div class="signatures">
        <div class="signature-container">
            <div class="signature-line"></div>
            <p class="signature-text">Entregado por</p>
        </div>
        <div class="signature-container">
            <div class="signature-line"></div>
            <p class="signature-text">Recibido por</p>
        </div>
    </div>
</body>
</html> 