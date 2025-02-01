<?php

namespace App\Filament\Resources\RegistrosMezclaResource\Pages;

use App\Filament\Resources\RegistrosMezclaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrosMezcla extends EditRecord
{
    protected static string $resource = RegistrosMezclaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Obtener el registro actualizado
        $record = $this->record;
        
        // Obtener la mezcla y sus materiales
        $mezcla = $record->mezcla;
        $cantidadMezcladas = $record->cantidad_mezcladas;
        
        // Obtener todos los materiales de la mezcla
        $materiales = $mezcla->mezclaMaterial;
        
        foreach ($materiales as $material) {
            $cantidadTotal = $material->cantidad * $cantidadMezcladas;
            
            if ($material->tipo_material === 'insumo') {
                $insumo = \App\Models\Insumos::find($material->insumo_id);
                if ($insumo) {
                    $nuevoStock = $insumo->stock - $cantidadTotal;
                    $insumo->update(['stock' => $nuevoStock]);
                    $insumo->save();
                }
            } elseif ($material->tipo_material === 'reprocesado') {
                $reprocesado = \App\Models\Reprocesados::find($material->reprocesado_id);
                if ($reprocesado) {
                    $nuevoPeso = $reprocesado->peso - $cantidadTotal;
                    $reprocesado->update(['peso' => $nuevoPeso]);
                    $reprocesado->save();
                }
            }
        }
    }
}
