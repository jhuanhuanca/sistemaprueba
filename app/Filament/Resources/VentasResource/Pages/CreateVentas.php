<?php

namespace App\Filament\Resources\VentasResource\Pages;

use App\Filament\Resources\VentasResource;
use App\Models\ventas;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVentas extends CreateRecord
{
    protected static string $resource = VentasResource::class;
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('vender')
                ->label('Vender')
                ->button()
                ->color('primary')
                ->action(function () {
                    // Obtener los datos del formulario
                    $data = $this->form->getState();

                    // Crear una nueva venta usando los datos del formulario
                    $venta=ventas::create([
                        'codigo'=> $data['codigo'],
                        'cliente_id'=>$data['cliente_id'],
                        'fecha_venta'=>$data['fecha_venta'],
                        'usuario'=>$data['usuario'],
                        'total'=>$data['total'],
                        'metodo_pago'=>$data['metodo_pago'],
                        'estado'=>$data['estado'],// o el estado que corresponda
                        // Agregar otros campos necesarios aquí
                    ]);

                    $this->redirect(VentasResource::getUrl('edit', ['record' => $venta->id]));
                }),
        ];
    }
}
