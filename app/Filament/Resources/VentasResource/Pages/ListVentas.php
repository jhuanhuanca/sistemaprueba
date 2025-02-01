<?php

namespace App\Filament\Resources\VentasResource\Pages;

use App\Exports\VentasExport;
use App\Filament\Resources\VentasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListVentas extends ListRecords
{
    protected static string $resource = VentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('nueva venta'),
            Actions\Action::make('pdf')
            ->label('pdf')
            ->button()
            ->color('danger')
            ->action(function ($record) {
                // Aquí puedes añadir la lógica para procesar la venta usando el registro actual
                // Ejemplo: Cambiar el estado de la venta, calcular el total, etc.
                Notification::make()
                    ->title("reporte: {$record->codigo}")
                    ->success()
                    ->send();
            }),
            Actions\Action::make('excel')
            ->label('excel')
            ->button()
            ->color('success')
            ->action(function () {
                // Aquí puedes añadir la lógica para procesar la venta usando el registro actual
                // Ejemplo: Cambiar el estado de la venta, calcular el total, etc.
                return Excel::download(new VentasExport, 'ventas.xlsx');
                
            }),
        ];
    }
}
