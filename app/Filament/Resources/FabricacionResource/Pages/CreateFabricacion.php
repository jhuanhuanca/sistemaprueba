<?php

namespace App\Filament\Resources\FabricacionResource\Pages;

use App\Filament\Resources\FabricacionResource;
use App\Models\asientos;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFabricacion extends CreateRecord
{
    protected static string $resource = FabricacionResource::class;

    protected function afterCreate(): void
    {
        // Obtener el ID del asiento de producción
        $asientoProduccion = asientos::where('descripcion', 'produccion')->first();
        
        if ($asientoProduccion) {
            $this->record->procesos()->create([
                'codigo' => $this->record->codigo,
                'fabricacion_id' => $this->record->id,
                'asiento_id' => $asientoProduccion->id,
                'descripcion' => 'Proceso de producción '
            ]);
        }
    }
}
