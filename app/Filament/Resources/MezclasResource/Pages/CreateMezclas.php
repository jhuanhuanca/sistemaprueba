<?php

namespace App\Filament\Resources\MezclasResource\Pages;

use App\Filament\Resources\MezclasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateMezclas extends CreateRecord
{
    protected static string $resource = MezclasResource::class;

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Elegir materiales')
            ->submit('create');
    }

    

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
