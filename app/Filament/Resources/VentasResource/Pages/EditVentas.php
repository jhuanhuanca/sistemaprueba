<?php

namespace App\Filament\Resources\VentasResource\Pages;

use App\Filament\Resources\VentasResource;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVentas extends EditRecord
{
    protected static string $resource = VentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('realizar_venta')
            ->label('Realizar Venta')
            ->button()
            ->color('success')
            ->action(function ($record) {
                $this->save();
                
                if ($record->estado === 'procesando ...') {
                    $record->estado = 'completada';
                    $record->save();
                    
                    Notification::make()
                        ->title("Venta realizada para el código: {$record->codigo}")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('la venta esta realizada a credito')
                        ->body('la venta esta realizada a credito el cliente esta adeudado')
                        ->warning()
                        ->send();
                        $this->redirect(VentasResource::getUrl('index'));
                        // Aquí puedes añadir la lógica para procesar la venta usando el registro actual
                        $venta = $record->load('DetalleVentas.productos');
                        $pdf = FacadePdf::loadView('pdf.facturas', ['venta' => $venta]);
                        return response()->streamDownload(
                            fn () => print($pdf->stream()),
                            "factura_{$record->codigo}.pdf"
                        );
                }

                $this->redirect(VentasResource::getUrl('index'));
                // Aquí puedes añadir la lógica para procesar la venta usando el registro actual
                $venta = $record->load('DetalleVentas.productos');
                $pdf = FacadePdf::loadView('pdf.facturas', ['venta' => $venta]);

        // Descargar el PDF
        return response()->streamDownload(
            fn () => print($pdf->stream()),
            "factura_{$record->codigo}.pdf"
        ); 
        
            }),
        ];
    }
}
