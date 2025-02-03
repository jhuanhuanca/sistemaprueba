<?php

namespace App\Filament\Resources\EntregasResource\Pages;

use App\Filament\Resources\EntregasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregas extends ListRecords
{
    protected static string $resource = EntregasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Entrega'),
        ];
    }
}
