<?php

namespace App\Filament\Resources\CategoriasResource\Pages;

use App\Filament\Resources\CategoriasResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategorias extends ManageRecords
{
    protected static string $resource = CategoriasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
