<?php

namespace App\Filament\Resources\OtrosCostosResource\Pages;

use App\Filament\Resources\OtrosCostosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOtrosCostos extends EditRecord
{
    protected static string $resource = OtrosCostosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
