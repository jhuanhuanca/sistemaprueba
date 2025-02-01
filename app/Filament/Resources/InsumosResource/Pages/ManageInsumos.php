<?php

namespace App\Filament\Resources\InsumosResource\Pages;

use App\Filament\Resources\InsumosResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInsumos extends ManageRecords
{
    protected static string $resource = InsumosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo Registro'),
        ];
    }
}
