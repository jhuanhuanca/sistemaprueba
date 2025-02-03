<!-- ver-fabricacion.blade.php -->
<div>
    <h2>Detalles de Fabricación</h2>
    <p><strong>Código:</strong> {{ $fabricacion->codigo }}</p>
    <p><strong>Área:</strong> {{ $fabricacion->area }}</p>
    <p><strong>Producto:</strong> {{ $fabricacion->producto }}</p>
    <p><strong>Tipo de Material:</strong> {{ $fabricacion->tipo_material }}</p>
    <p><strong>Fecha de finalizacion :</strong> {{ $fabricacion->fecha_finalizacion }}</p>
    <p>
    <strong>Estado:</strong> 
        {{ $fabricacion->estado == 1 ? 'Activo' : 'Inactivo' }}
    </span>
</p>
    <!-- Agrega más campos según sea necesario -->
</div>