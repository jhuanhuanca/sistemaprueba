<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generarCotizacion')
                ->label('Generar Cotización')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->url(route('generar.cotizacion', ['pedido' => $this->record->id]))
                ->openUrlInNewTab(),

            Actions\Action::make('generarPedido')
                ->label('Generar Pedido')
                ->color('primary')
                ->icon('heroicon-o-document')
                ->url(route('generar.pedido', ['pedido' => $this->record->id]))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make(),
        ];
    }
}