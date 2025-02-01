<?php

namespace App\Filament\Resources\ProveedoresResource\Pages;

use App\Filament\Resources\ProveedoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProveedores extends ManageRecords
{
    protected static string $resource = ProveedoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo Registro'),
        ];
    }
}
