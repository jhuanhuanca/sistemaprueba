<?php

namespace App\Filament\Resources\FabricacionResource\Pages;

use App\Filament\Resources\FabricacionResource;
use Filament\Resources\Pages\Page;
use App\Models\Fabricacion;

class ListaCompleta extends Page
{
    protected static string $resource = FabricacionResource::class;

    protected static string $view = 'filament.resources.fabricacion.pages.lista-completa';

    public function getFabricaciones()
    {
        return Fabricacion::all();
    }
} 