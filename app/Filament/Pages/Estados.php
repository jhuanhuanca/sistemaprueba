<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\procesos;
use App\Models\asientos;

class Estados extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static string $view = 'filament.pages.estados';
    protected static ?string $navigationGroup = 'SEGUIMIENTO DE PRODUCCION';
    
    public $procesos;
    public $asientos;
    
    public function mount()
    {
        // Obtener solo los procesos únicos que están asociados a asientos
        $this->procesos = procesos::whereHas('asientos')
                                ->select('descripcion')
                                ->distinct()
                                ->orderBy('descripcion')
                                ->get();
                                
        $this->asientos = asientos::with('procesos')->get();
    }

    protected function getViewData(): array
    {
        return [
            'procesos' => $this->procesos,
            'asientos' => $this->asientos,
        ];
    }
}


