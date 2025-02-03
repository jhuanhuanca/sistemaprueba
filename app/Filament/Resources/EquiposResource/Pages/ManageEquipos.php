<?php

namespace App\Filament\Resources\EquiposResource\Pages;

use App\Filament\Resources\EquiposResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEquipos extends ManageRecords
{
    protected static string $resource = EquiposResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('crear'),
        ];
    }
}
