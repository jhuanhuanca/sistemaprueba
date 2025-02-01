<?php

namespace App\Filament\Resources\ControlCalidadResource\Pages;

use App\Filament\Resources\ControlCalidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListControlCalidads extends ListRecords
{
    protected static string $resource = ControlCalidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
