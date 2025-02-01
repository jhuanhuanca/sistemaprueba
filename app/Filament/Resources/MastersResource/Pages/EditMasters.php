<?php

namespace App\Filament\Resources\MastersResource\Pages;

use App\Filament\Resources\MastersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasters extends EditRecord
{
    protected static string $resource = MastersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
