<?php

namespace App\Filament\Resources\CostosProduccionsResource\Pages;

use App\Filament\Resources\CostosProduccionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCostosProduccions extends EditRecord
{
    protected static string $resource = CostosProduccionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
