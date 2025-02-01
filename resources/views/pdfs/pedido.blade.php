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
            margin-bottom: 20px;
        }
        .header-left {
            float: left;
        }
        .header-right {
            float: right;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
        }
        .signatures {
            margin-top: 70px;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <strong>ZAVIT & ROWLAND S.R.L.</strong><br>
            Alm casa matriz
        </div>
        <div class="header-right">
            Pag. 1<br>
            Fecha: {{ $fecha }}<br>
            Hrs: {{ $hora }}
        </div>
    </div>
    <div class="clear"></div>

    <div class="title">
        {{ $pedido->codigo }}<br>
        PEDIDO DE VENTA
    </div>

    <div class="customer-info">
        Señor(es): {{ $pedido->clienteRelacion->nombre }}<br>
        Teléfono: {{ $pedido->telefono }}<br>
        Celular: -
    </div>

    <table>
        <thead>
            <tr>
                <th>CÓDIGO</th>
                <th>Descripción</th>
                <th>Cant</th>
                <th>Uni</th>
                <th>PRECIO</th>
                <th>TOTAL Bs</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($pedido->detalles_pedido as $detalle)
            <tr>
                <td>{{ $detalle->producto->codigo ?? '-' }}</td>
                <td>{{ $detalle->producto->nombre }}
                    @if($detalle->subproducto_id)
                        - {{ $detalle->subproducto->nombre }}
                    @endif
                </td>
                <td>{{ $detalle->cantidad }}</td>
                <td>und</td>
                <td>{{ number_format($detalle->precio_unitario, 2) }}</td>
                <td>{{ number_format($detalle->total, 2) }}</td>
            </tr>
            @php $total += $detalle->total; @endphp
            @endforeach
            <tr>
                <td colspan="5" style="text-align: right;"><strong>TOTAL Bs:</strong></td>
                <td><strong>{{ number_format($total, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 10px;">
        Impreso: {{ now()->format('Y-m-d') }} a hrs. {{ now()->format('H:i:s') }}
    </div>

    <div class="signatures">
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: center; width: 50%;">
                    <div class="signature-line">
                        Vendedor<br>
                        {{ auth()->user()->name ?? 'Vendedor' }}
                    </div>
                </td>
                <td style="border: none; text-align: center; width: 50%;">
                    <div class="signature-line">
                        Depto. Comercial
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html> 