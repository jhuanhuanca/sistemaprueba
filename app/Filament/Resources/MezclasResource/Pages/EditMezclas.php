<?php

namespace App\Filament\Resources\MezclasResource\Pages;

use App\Filament\Resources\FabricacionResource;
use App\Filament\Resources\MezclasResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditMezclas extends EditRecord
{
    protected static string $resource = MezclasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('realizarMezcla')
                ->label('Realizar mezcla')
                ->url(FabricacionResource::getUrl('index'))
                ->color('success')
                ->submit('save')
                ->button(),
        ];
    }
    protected function getFormActions(): array
    {
        return [
        ];
    }
}

