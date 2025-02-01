<?php

namespace App\Filament\Resources\ProduccionFinalResource\Pages;

use App\Filament\Resources\ProduccionFinalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProduccionFinals extends ListRecords
{
    protected static string $resource = ProduccionFinalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
