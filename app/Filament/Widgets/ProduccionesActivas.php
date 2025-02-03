<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Filament\Resources\FabricacionResource;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProduccionesActivas extends BaseWidget
{

    protected static ?int $sort =4;
    protected static ?string $label = 'Produccion Activas';
    protected int |string|array $columnSpan = 'full';    public function table(Table $table): Table
    {
        return $table
        ->query((FabricacionResource::getEloquentQuery()))
        ->defaultPaginationPageOption(5)
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                ->label('#')
                ->rowIndex(),
            Tables\Columns\TextColumn::make('codigo')
                ->searchable(),
            Tables\Columns\TextColumn::make('OrdenProduccion.codigo')->label('Código de Producción')
                ->sortable()
                ->label('Orden'),
            Tables\Columns\TextColumn::make('area')
                ->searchable(),
            Tables\Columns\TextColumn::make('producto')
                ->searchable()
                ->label('Producto'),
            Tables\Columns\TextColumn::make('Equipos.nombre')
            ->label('maquina')
                ->searchable(),
            Tables\Columns\TextColumn::make('tipo_material')
                ->searchable(),
            Tables\Columns\TextColumn::make('estado')
                ->label('Estado')
                ->formatStateUsing(fn ($state) => $state == 1 ? 'Activo' : 'Inactivo')
                ->color(fn ($state) => $state == 1 ? 'success' : 'danger')
                ->searchable(),

            ]);
            
    }
}
