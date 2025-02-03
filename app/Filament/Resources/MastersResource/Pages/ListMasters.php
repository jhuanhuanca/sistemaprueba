<?php

namespace App\Filament\Resources\MastersResource\Pages;

use App\Filament\Resources\MastersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasters extends ListRecords
{
    protected static string $resource = MastersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
