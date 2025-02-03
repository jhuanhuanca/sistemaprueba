<?php

namespace App\Filament\Widgets;

use App\Models\Insumos;
use App\Models\OrdenProduccion;
use App\Models\productos;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TestWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Users',User::count())
            ->description('nuevos usuarios registrados')
            ->descriptionIcon('heroicon-m-user-group',IconPosition::Before)
            ->chart([1,3,5,0,20,40])
            ->color('success'),
            Stat::make('Productos',productos::count())
            ->description('Productos Registrados en Almacen')
            ->descriptionIcon('heroicon-o-table-cells',IconPosition::Before)
            ->chart([1,3,5,0,20,40])
            ->color('warning'),
            Stat::make('Insumos',Insumos::count())
            ->description('Insumos Registrados en Almacen')
            ->descriptionIcon('heroicon-o-table-cells',IconPosition::Before)
            ->chart([1,3,5,0,20,40])
            ->color('warning'),
            Stat::make('Ordenes Produccion',OrdenProduccion::count())
            ->description('Ordenes de Produccion Registrados')
            ->descriptionIcon('heroicon-o-table-cells',IconPosition::Before)
            ->chart([1,3,5,0,20,40])
            ->color('warning'),
        ];
    }
}
