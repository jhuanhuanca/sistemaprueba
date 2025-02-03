<?php

namespace App\Filament\Resources\CostosProduccionsResource\Pages;

use App\Filament\Resources\CostosProduccionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCostosProduccions extends ListRecords
{
    protected static string $resource = CostosProduccionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
