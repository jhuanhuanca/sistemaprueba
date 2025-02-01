<?php

namespace App\Filament\Resources\ProduccionDiariaResource\Pages;

use App\Filament\Resources\ProduccionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduccionDiaria extends EditRecord
{
    protected static string $resource = ProduccionDiariaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
