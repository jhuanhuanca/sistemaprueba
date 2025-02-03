<div class="space-y-4">
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4 dark:text-white">Detalles de la Venta {{ $venta->codigo }}</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="dark:text-gray-200">
                <p><strong>Cliente:</strong> {{ $venta->clientes->nombre }}</p>
                <p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>
                <p><strong>Usuario:</strong> {{ $venta->usuario }}</p>
            </div>
            <div class="dark:text-gray-200">
                <p><strong>Total:</strong> {{ $venta->total }}</p>
                <p><strong>Método de Pago:</strong> {{ $venta->metodo_pago }}</p>
                <p><strong>Estado:</strong> {{ $venta->estado }}</p>
            </div>
        </div>

        <h3 class="text-lg font-semibold mb-2 dark:text-white">Productos</h3>
        <table class="w-full">
            <thead>
                <tr class="dark:text-gray-200">
                    <th class="text-left">Producto</th>
                    <th class="text-left">Cantidad</th>
                    <th class="text-left">Precio</th>
                    <th class="text-left">Subtotal</th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-300">
                @foreach($venta->detalleVentas as $detalle)
                    <tr>
                        <td>{{ $detalle->productos->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ $detalle->precio_unitario }}</td>
                        <td>{{ $detalle->subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 