<?php

namespace App\Filament\Resources\FabricacionResource\Pages;

use App\Filament\Resources\FabricacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditFabricacion extends EditRecord
{
    protected static string $resource = FabricacionResource::class;
    
    public function getTitle(): string
    {
        return 'Editar Fabricación'; // Cambia el título de la página
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('guardarCambios')
                ->label('Guardar Cambios')
                ->action(function () {
                    // Guardar el registro
                    $this->save();
                    
                    // Verificar si el estado ha cambiado a false
                    if ($this->record->estado === false) {
                        // Verificar si hay producciones finales
                        $totalProducido = \App\Models\ProduccionFinal::where('fabricacion_id', $this->record->id)
                            ->sum('cantidad');
                            
                        if ($totalProducido < $this->record->cantidad_a_producir) {
                            Notification::make()
                                ->title('Advertencia')
                                ->warning()
                                ->body('La fabricación se ha desactivado antes de alcanzar la cantidad objetivo.')
                                ->send();
                        }
                    }

                    Notification::make()
                        ->title('Guardado exitosamente')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->color('success'),
            Actions\DeleteAction::make()
                ->label('Eliminar Registro'),
            Actions\Action::make('cancelar')
                ->label('Cancelar')
                ->url($this->getResource()::getUrl('index'))
                ->color('primary'),
        ];
    }
    protected function getFormActions(): array
    {
        return []; // Desactiva los botones de formulario predeterminados
    }
}
