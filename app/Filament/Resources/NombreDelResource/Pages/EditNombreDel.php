<?php

namespace App\Filament\Resources\NombreDelResource\Pages;

use App\Filament\Resources\NombreDelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNombreDel extends EditRecord
{
    protected static string $resource = NombreDelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
