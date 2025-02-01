<?php

namespace App\Filament\Resources\ReprocesadosResource\Pages;

use App\Filament\Resources\ReprocesadosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReprocesados extends ListRecords
{
    protected static string $resource = ReprocesadosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
