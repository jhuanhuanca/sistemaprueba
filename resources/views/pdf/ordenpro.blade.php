<div style="margin: 20px;">
    <!-- Encabezado con logo -->
    <div style="display: flex; justify-content: space-between; align-items: start;">
        <div>
            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" width="100">
            <p style="margin: 5px 0; font-size: 14px;"><strong>ZAVIT & ROWLAND S.R.L.</strong></p>
        </div>
        <div style="text-align: right; font-size: 12px;">
            <p style="margin: 2px 0;">Pag. 1</p>
            <p style="margin: 2px 0;">Fecha: {{ now()->format('d-m-Y') }}</p>
            <p style="margin: 2px 0;">Hrs: {{ now()->format('H:i:s') }}</p>
        </div>
    </div>

    <!-- Número de orden y título -->
    <div style="text-align: center; margin: 20px 0;">
        <h2 style="margin: 5px 0;">{{ $orden->codigo }}</h2>
        <h3 style="margin: 5px 0;">ORDEN DE PRODUCCIÓN</h3>
    </div>

    <!-- Datos del cliente y fechas -->
    <div style="margin-bottom: 15px; font-size: 12px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <div>
                <p style="margin: 2px 0;"><strong>Señor(es):</strong> {{ $orden->clientes?->nombre ?? 'No asignado' }}</p>
                <p style="margin: 2px 0;">
                    <strong>Teléfono:</strong> {{ $orden->clientes?->telefono ?? 'No especificado' }}
                    <span style="margin-left: 20px;"><strong>Celular:</strong> {{ $orden->clientes?->telefono ?? 'No especificado' }}</span>
                </p>
            </div>
            <div>
                <p style="margin: 2px 0;"><strong>Área:</strong> {{ $orden->area ?? 'No especificada' }}</p>
                <p style="margin: 2px 0;"><strong>Fecha Estimada:</strong> {{ $orden->fecha_estimada ?? 'No especificada' }}</p>
                <p style="margin: 2px 0;"><strong>Fecha Entrega:</strong> {{ $orden->fecha_entrega ?? 'No especificada' }}</p>
            </div>
        </div>
    </div>

    <!-- Tabla de detalles -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #000; padding: 6px; text-align: left;">CÓDIGO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left;">Descripción</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Cant</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Uni</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Color</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 6px;">{{ $orden->productos?->codigo ?? 'N/A' }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $orden->productos?->nombre ?? 'No especificado' }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $orden->cantidad_producir ?? 'N/A' }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">Uds.</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $orden->color ?? 'No especificado' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Firmas -->
    <div style="margin-top: 80px; font-size: 12px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div style="margin: 0 auto; width: 200px;">
                        <div style="border-top: 1px solid #000;">
                            <p style="margin: 5px 0;">Firma Administrativo</p>
                        </div>
                    </div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="margin: 0 auto; width: 200px;">
                        <div style="border-top: 1px solid #000;">
                            <p style="margin: 5px 0;">Firma Jefe de Planta</p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Información de impresión -->
    <div style="margin-top: 20px; font-size: 10px; color: #666;">
        <p>Impreso: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</div>
