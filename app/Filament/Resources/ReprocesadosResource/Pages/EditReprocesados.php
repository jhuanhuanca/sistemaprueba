<?php

namespace App\Filament\Resources\ReprocesadosResource\Pages;

use App\Filament\Resources\ReprocesadosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReprocesados extends EditRecord
{
    protected static string $resource = ReprocesadosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
