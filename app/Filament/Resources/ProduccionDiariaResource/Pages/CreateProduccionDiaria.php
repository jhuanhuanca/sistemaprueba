<?php

namespace App\Filament\Resources\ProduccionDiariaResource\Pages;

use App\Filament\Resources\ProduccionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduccionDiaria extends CreateRecord
{
    protected static string $resource = ProduccionDiariaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
