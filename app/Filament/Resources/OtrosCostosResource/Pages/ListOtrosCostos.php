<?php

namespace App\Filament\Resources\OtrosCostosResource\Pages;

use App\Filament\Resources\OtrosCostosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOtrosCostos extends ListRecords
{
    protected static string $resource = OtrosCostosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
