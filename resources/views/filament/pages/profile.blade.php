<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Información del Usuario --}}
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <img class="h-20 w-20 rounded-full" src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar">
                </div>
                <div>
                    <h3 class="text-lg font-medium">{{ $user->name }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- Botón para mostrar/ocultar formulario --}}
        <x-filament::button
            wire:click="$toggle('showEditForm')"
            class="mt-4"
        >
            {{ $showEditForm ? 'Cancelar Edición' : 'Modificar Perfil' }}
        </x-filament::button>

        {{-- Formulario de edición --}}
        @if($showEditForm)
            <div class="mt-6">
                {{ $this->form }}

                <x-filament::button 
                    type="submit" 
                    wire:click="submit"
                    class="mt-4"
                >
                    Guardar cambios
                </x-filament::button>
            </div>
        @endif
    </div>
</x-filament-panels::page> 