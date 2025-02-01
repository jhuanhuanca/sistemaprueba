<?php

namespace App\Filament\Resources\RegistrosMezclaResource\Pages;

use App\Filament\Resources\RegistrosMezclaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrosMezclas extends ListRecords
{
    protected static string $resource = RegistrosMezclaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
