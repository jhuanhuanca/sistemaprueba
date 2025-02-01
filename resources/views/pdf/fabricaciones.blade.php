<div style="margin: 20px;">
    <!-- Encabezado -->
    <div style="display: flex; justify-content: space-between; align-items: start;">
        <div>
            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" width="100">
            <p style="margin: 5px 0; font-size: 14px;"><strong>ZAVIT & ROWLAND S.R.L.</strong></p>
        </div>
        <div style="text-align: right; font-size: 12px;">
            <p style="margin: 2px 0;">Fecha: {{ now()->format('d-m-Y') }}</p>
            <p style="margin: 2px 0;">Hrs: {{ now()->format('H:i:s') }}</p>
        </div>
    </div>

    <h2 style="text-align: center;">Listado de Órdenes de Fabricación</h2>

    <!-- Tabla de fabricaciones -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #000; padding: 6px;">Código</th>
                <th style="border: 1px solid #000; padding: 6px;">Fecha Finalización</th>
                <th style="border: 1px solid #000; padding: 6px;">Área</th>
                <th style="border: 1px solid #000; padding: 6px;">Producto</th>
                <th style="border: 1px solid #000; padding: 6px;">Máquina</th>
                <th style="border: 1px solid #000; padding: 6px;">Material</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fabricaciones as $fabricacion)
            <tr>
                <td style="border: 1px solid #000; padding: 6px;">{{ $fabricacion->codigo }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ \Carbon\Carbon::parse($fabricacion->fecha_finalizacion)->format('d-m-Y') }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $fabricacion->area }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $fabricacion->producto }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $fabricacion->equipos->nombre }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $fabricacion->tipo_material }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Información de impresión -->
    <div style="margin-top: 20px; font-size: 10px; color: #666;">
        <p>Impreso: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</div> 