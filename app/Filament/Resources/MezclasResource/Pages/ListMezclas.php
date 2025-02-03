<?php

namespace App\Filament\Resources\MezclasResource\Pages;

use App\Filament\Resources\MezclasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMezclas extends ListRecords
{
    protected static string $resource = MezclasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
