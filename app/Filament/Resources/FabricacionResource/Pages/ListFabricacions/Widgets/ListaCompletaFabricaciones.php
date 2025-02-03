<?php

namespace App\Filament\Resources\FabricacionResource\Pages\ListFabricacions\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Fabricacion;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ListaCompletaFabricaciones extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = false;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Fabricacion::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('codigo'),
            TextColumn::make('fecha_finalizacion')
                ->date()
                ->sortable(),
            TextColumn::make('area'),
            TextColumn::make('producto'),
            TextColumn::make('equipos.nombre')
                ->label('Máquina'),
            TextColumn::make('tipo_material'),
            Tables\Columns\ToggleColumn::make('estado')
                ->label('Estado'),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Listado Completo de Fabricaciones';
    }

    protected function getTableContentGrid(): array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 1,
        ];
    }

    public function getTableStriped(): bool
    {
        return true;
    }

    public function getMaxHeight(): ?string
    {
        return 'calc(100vh - 12rem)';
    }

  
} 