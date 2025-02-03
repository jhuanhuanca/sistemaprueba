<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: right;
            margin-bottom: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
        }
        .total {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        {{ $fecha }}<br>
        {{ $codigo }}
    </div>

    <div class="logo">
        <!-- Aquí puedes agregar tu logo -->
        ROWLAND
    </div>

    <div class="title">
        COTIZACIÓN
    </div>

    <div class="customer-info">
        Señor(es):<br>
        {{ $pedido->clienteRelacion->nombre }}<br>
        Teléfono: {{ $pedido->telefono }}<br>
    </div>

    <div>
        De nuestra consideración:
    </div>

    <div>
        Tenemos el agrado de dirigirnos a ustedes para atender su pedido de cotización:
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Cant</th>
                <th>Und</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($pedido->detalles_pedido as $index => $detalle)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>und</td>
                <td>{{ $detalle->producto->nombre }} 
                    @if($detalle->subproducto_id)
                        - {{ $detalle->subproducto->nombre }}
                    @endif
                </td>
                <td>{{ number_format($detalle->precio_unitario, 2) }}</td>
                <td>{{ number_format($detalle->total, 2) }}</td>
            </tr>
            @php $total += $detalle->total; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <strong>Total: S/. {{ number_format($total, 2) }}</strong>
    </div>

    <div style="margin-top: 30px">
        <strong>Tiempo de entrega:</strong> A COORDINAR<br>
        <strong>Validez:</strong> 15 DÍAS<br>
        <strong>Forma de pago:</strong> CONTRA ENTREGA DEL PRODUCTO<br>
        <strong>Lugar de entrega:</strong> {{ $pedido->almacen->descripcion }}
    </div>

    <div style="margin-top: 50px">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; text-align: center; width: 50%;">
                    ____________________<br>
                    Depto. Técnico
                </td>
                <td style="border: none; text-align: center; width: 50%;">
                    ____________________<br>
                    Depto. Comercial
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong>Datos para depósito:</strong><br>
        Banco Unión en Bs. Cuenta: XXXXXXXXXX<br>
        Banco Ganadero en Bs. Cuenta: XXXXXXXXXX<br>
        Banco Bisa en Bs. Cuenta: XXXXXXXXXX
    </div>
</body>
</html> 