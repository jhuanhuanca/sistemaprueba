<?php

namespace App\Filament\Resources\EntregasResource\Pages;

use App\Filament\Resources\EntregasResource;
use Filament\Resources\Pages\CreateRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;
use Filament\Actions\Actions;

class CreateEntregas extends CreateRecord
{
    protected static string $resource = EntregasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('realizar_entrega')
            ->label('Realizar Entrega')
            ->button()
            ->color('success') 
            ->action(function () {
                $this->create();
                
                // Notificación simple
                Notification::make()
                    ->title("Entrega realizada para el código: {$this->record->codigo}")
                    ->success()
                    ->send();
                    $this->redirect(EntregasResource::getUrl('index'));
                // Cargar las relaciones necesarias
                $entrega = $this->record->load('ordenproduccion');
                
                // Generar el PDF
                $pdf = Pdf::loadView('pdf.recibo-entrega', ['entrega' => $entrega]);

                // Descargar el PDF
                return response()->streamDownload(
                    fn () => print($pdf->stream()),
                    "recibo-entrega-{$this->record->codigo}.pdf"
                );
            }),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancelar')
            ->color('gray')
            ->url($this->previousUrl);
    }
}
