<?php

namespace App\Filament\Resources\FabricacionResource\Pages;

use App\Filament\Resources\FabricacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Fabricacion;

class ListFabricacions extends ListRecords
{
    protected static string $resource = FabricacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ListFabricacions\Widgets\ListaCompletaFabricaciones::class,
        ];
    }
}
