<?php

namespace App\Filament\Resources\OrdenProduccionResource\Pages;

use App\Filament\Resources\OrdenProduccionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Filament\Resources\Pages\EditRecord;

class EditOrdenProduccion extends EditRecord
{
    protected static string $resource = OrdenProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Eliminar'),
            Actions\Action::make('imprimir_orden')
                ->label('orden')
                ->icon('heroicon-o-printer')
                ->button()
                ->color('primary')
                ->action(function () {
                    // Buscar el registro de la orden de producción existente basado en un criterio único
                    $record = $this->form->getModel()::where( $this->form->getState())->first();
                
                    if (!$record) {
                        // Opcionalmente, podrías lanzar una excepción si el registro no existe
                        Notification::make()
                            ->title("La orden de producción no existe.")
                            ->danger()
                            ->send();
                
                        return;
                    }
                
                    // Actualizar el estado de la orden
                    $record->estado = 'en marcha';
                    $record->save();
                
                    // Notificar que la orden ha sido actualizada
                    Notification::make()
                        ->title("Orden de Producción: {$record->codigo} actualizada")
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
        ];
    }
}
