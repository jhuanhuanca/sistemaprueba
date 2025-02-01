<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Fabricacion;
use App\Models\ProduccionDiaria;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class FabricacionesPage extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string $view = 'filament.pages.fabricaciones-page';
    protected static ?string $navigationGroup = 'PLANIFICACION DE PRODUCCION';
    protected static ?string $title = 'Produccion Activas';

    public static function table(Table $table): Table
    {
        
        return $table
        
        ->defaultSort('created_at', 'desc') 
        ->query(Fabricacion::query()->where('estado', 1))
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
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
            ])
            ->filters([
                Filter::make('fecha_finalizacion')
                ->label('Filtrar por Fecha de Finalización')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde')
                            ->placeholder('Selecciona una fecha de inicio'),
                        DatePicker::make('hasta')
                            ->label('Hasta')
                            ->placeholder('Selecciona una fecha de fin'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['desde']) {
                            $query->where('fecha_finalizacion', '>=', $data['desde']);
                        }
                        if ($data['hasta']) {
                            $query->where('fecha_finalizacion', '<=', $data['hasta']);
                        }
                    }),
            ])
            ->actions([  
                Tables\Actions\Action::make('ver')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalles de Fabricación')
                    ->modalContent(fn ($record) => view('modals.ver-fabricacion', ['fabricacion' => $record]))
                    ->modalWidth('lg'),//
                    Tables\Actions\Action::make('Ir a Producción Diaria')
                        ->url(fn ($record) => route('filament.Dashboard.resources.produccion-diarias.create', [
                            'codigo' => $record->codigo,
                            'producto'=>$record->producto,
                            'equipo_id'=>$record->equipo_id,
                        ]))
                        ->button()
                        ->size('xs')
                        ->color('success')
                        ->label('Registrar Producción'),
                        Tables\Actions\Action::make('Ir a Reguistar Mezcla')
                        ->url(fn ($record) => route('filament.Dashboard.resources.registros-mezclas.create', [
                            'codigo' => $record->codigo ,
                            'mezcla_id'=>$record->mezcla_id,
                            'cantidad_por_mezclar'=>$record->cantidad_mezclas,
                        ]))
                        ->button()
                        ->size('xs')
                        ->color('success')
                        ->label('Registrar Mezcla'),
            ])
            
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
            
    }
    public function mostrarFabricacion($id)
{
    $fabricacion = Fabricacion::findOrFail($id);
    return view('modals.ver-fabricacion', compact('fabricacion'));
}
protected function getDefaultTableSortColumn(): ?string
{
    return 'created_at';
}

protected function getDefaultTableSortDirection(): ?string
{
    return 'desc';
}
public static function getNavigationBadge(): ?string
{
    return Fabricacion::where('estado', 1)->count();
}
}
