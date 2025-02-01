<?php

namespace App\Filament\Resources\OrdenProduccionResource\Pages;

use App\Filament\Resources\OrdenProduccionResource;
use App\Models\OrdenProduccion;
use Filament\Actions;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOrdenProduccion extends CreateRecord
{
    protected static string $resource = OrdenProduccionResource::class;
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('realizar_orden')
                ->label('Generar Orden')
                ->icon('heroicon-o-document')
                ->button()
                ->color('primary')
                ->action(function () {
                    // Crear y guardar el registro de la orden de producción
                    $record = $this->form->getModel()::create($this->form->getState());
    
                    // Actualizar el estado de la orden
                    $record->estado = 'en marcha';
                    $record->save();
    
                    // Notificar que la orden ha sido generada
                    Notification::make()
                        ->title("Orden de Producción : {$record->codigo}")
                        ->success()
                        ->send();
                        // Redirigir a la página de índice de órdenes de producción
                $this->redirect(OrdenProduccionResource::getUrl('index'));
                    // Generar el PDF con los datos de la orden
                    $pdf = FacadePdf::loadView('pdf.ordenpro', ['orden' => $record]);
    
                    // Descargar el PDF de la orden de producción
                    return response()->streamDownload(
                        fn () => print($pdf->stream()),
                        "ordenpro_{$record->codigo}.pdf"
                    );
                }),
                Actions\Action::make('cancelar')
    ->label('Cancelar') // Etiqueta del botón
    ->icon('heroicon-o-arrow-left') // Ícono opcional
    ->url(route('filament.Dashboard.resources.orden-produccions.index')) // URL de redirección
    ->color('danger') // Color del botón (opcional)
    ->requiresConfirmation(false), // No pide confirmación (opcional)
        ];
    }
    
}
