<?php

namespace App\Filament\Resources\AsientosResource\Pages;

use App\Filament\Resources\AsientosResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAsientos extends ManageRecords
{
    protected static string $resource = AsientosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
