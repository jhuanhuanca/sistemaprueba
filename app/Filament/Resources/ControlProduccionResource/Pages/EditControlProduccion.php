<?php

namespace App\Filament\Resources\ControlProduccionResource\Pages;

use App\Filament\Resources\ControlProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditControlProduccion extends EditRecord
{
    protected static string $resource = ControlProduccionResource::class;

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

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Control de producción actualizado')
            ->body('El registro ha sido actualizado exitosamente.');
    }
}
