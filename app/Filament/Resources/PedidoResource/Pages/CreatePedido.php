<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generarCotizacion')
                ->label('Generar Cotización')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->url(fn () => $this->record ? route('generar.cotizacion', ['pedido' => $this->record->id]) : null)
                ->visible(fn () => $this->record !== null)
                ->openUrlInNewTab(),

            Actions\Action::make('generarPedido')
                ->label('Generar Pedido')
                ->color('primary')
                ->icon('heroicon-o-document')
                ->url(fn () => $this->record ? route('generar.pedido', ['pedido' => $this->record->id]) : null)
                ->visible(fn () => $this->record !== null)
                ->openUrlInNewTab(),
        ];
    }
}