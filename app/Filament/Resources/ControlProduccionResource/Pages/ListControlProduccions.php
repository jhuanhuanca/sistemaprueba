<?php

namespace App\Filament\Resources\ControlProduccionResource\Pages;

use App\Filament\Resources\ControlProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListControlProduccions extends ListRecords
{
    protected static string $resource = ControlProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Producciones de Calidad')
                ->url(route('filament.Dashboard.pages.calidad'))
                ->button(),
        ];
    }
}
