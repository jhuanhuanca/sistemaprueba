<!-- resources/views/livewire/detalle-fabricacion.blade.php -->

<div>
    <!-- Modal de detalles de la fabricacion -->
    <x-filament::modal :open="true" id="detalle-fabricacion-modal">
        <x-slot name="title">
            Detalles de la Fabricación
        </x-slot>

        <x-slot name="content">
            @if ($fabricacion)
                <p><strong>ID:</strong> {{ $fabricacion->id }}</p>
                <p><strong>Producto:</strong> {{ $fabricacion->producto->nombre }}</p>
                <p><strong>Cantidad:</strong> {{ $fabricacion->cantidad }}</p>
                <p><strong>Fecha de Fabricación:</strong> {{ $fabricacion->fecha_fabricacion }}</p>
                <p><strong>Estado:</strong> {{ $fabricacion->estado }}</p>
                <!-- Agrega más campos según lo necesario -->
            @else
                <p>Fabricación no encontrada.</p>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-filament::button wire:click="$emit('closeModal')" color="secondary">
                Cerrar
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
