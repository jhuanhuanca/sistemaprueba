<?php

namespace App\Filament\Resources\ProduccionDiariaResource\Pages;

use App\Filament\Resources\ProduccionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListProduccionDiarias extends ListRecords
{
    protected static string $resource = ProduccionDiariaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ir_fabricaciones')
                ->label('Producciones activas')
                ->url(route('filament.Dashboard.pages.fabricaciones-page')),
        ];
    }

   
   //
}