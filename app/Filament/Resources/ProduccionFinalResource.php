<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduccionFinalResource\Pages;
use App\Filament\Resources\ProduccionFinalResource\RelationManagers;
use App\Models\ProduccionFinal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\FiltersAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;

class ProduccionFinalResource extends Resource
{
    protected static ?string $model = ProduccionFinal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'SEGUIMIENTO DE PRODUCCION';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('fabricacion_id')
    ->relationship('fabricacion', 'codigo') // Relación con Fabricación
    ->required()
    ->reactive(), // Para reaccionar a los cambios en este campo

    Forms\Components\Select::make('produccion_diaria_id')
    ->label('Producción Diaria')
    ->required()
    ->reactive() // Reactivo a cambios en otros campos
    ->options(function (callable $get) {
        $fabricacionId = $get('fabricacion_id'); // Obtén el ID de fabricación seleccionado
        if ($fabricacionId) {
            return \App\Models\ProduccionDiaria::where('fabricacion_id', $fabricacionId)
                ->get()
                ->mapWithKeys(function ($produccion) {
                    $nombreOperador = $produccion->usuario ?? 'Sin Operador';
                    return [ 
                        $produccion->id => ' ' . $produccion->numero_lote . ' - ' . $nombreOperador,
                    ];
                });
        }
        return [];
    })
    ->afterStateUpdated(function ($state, callable $set) {
        if ($state) {
            $produccion = \App\Models\ProduccionDiaria::find($state);
            if ($produccion) {
                // Actualizar automáticamente los campos cantidad y producto
                $set('cantidad', $produccion->cantidad_producida);
                $set('producto', $produccion->producto);
            }
        }
    }),
    
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->live()
                    ->numeric(),
                Forms\Components\TextInput::make('producto')
                    ->required()
                    ->maxLength(255),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fabricacion.codigo')
                ->label('Fabricación')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('producciondiaria.usuario')
                ->label('operador')
                    ->sortable(),
                Tables\Columns\TextColumn::make('producciondiaria.numero_lote')
                ->label('produccion')
                ->sortable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->label('Área')
                    ->relationship('fabricacion.ordenProduccion.areas', 'descripcion')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('fabricacion_id')
                    ->label('Código de Fabricación')
                    ->relationship('fabricacion', 'codigo')
                    ->searchable()
                    ->preload(),
            ],layout: Tables\Enums\FiltersLayout::AboveContent)
            ->filtersTriggerAction(
                fn (TableAction $action) => $action
                    ->button()
                    ->label('Filtros')
            )
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduccionFinals::route('/'),
            'create' => Pages\CreateProduccionFinal::route('/create'),
            'edit' => Pages\EditProduccionFinal::route('/{record}/edit'),
        ];
    }

    protected function afterCreate($record): void
    {
        // Verificar y actualizar el estado de fabricación
        $this->verificarYActualizarFabricacion($record->fabricacion_id);

        Notification::make()
            ->title('Producción Final guardada exitosamente')
            ->success()
            ->actions([
                NotificationAction::make('generar_cotizacion')
                    ->label('Generar Cotización')
                    ->button()
                    ->color('success')
                    ->url(route('generar.cotizacion.pdf', ['id' => $record->id]), shouldOpenInNewTab: true),
                NotificationAction::make('generar_pedido')
                    ->label('Generar Pedido')
                    ->button()
                    ->color('primary')
                    ->url(route('generar.pedido.pdf', ['id' => $record->id]), shouldOpenInNewTab: true),
            ])
            ->persistent()
            ->send();
    }


    protected function verificarYActualizarFabricacion($fabricacionId)
    {
        $fabricacion = \App\Models\Fabricacion::find($fabricacionId);
        
        if ($fabricacion) {
            // Obtener la suma total de todas las producciones totales para esta fabricación
            $totalProducido = \App\Models\ProduccionTotal::where('fabricacion_id', $fabricacionId)
                ->value('total_cantidad');

            // Notificación del total producido
            Notification::make()
                ->title('Total Producido')
                ->info()
                ->body("Cantidad total producida: {$totalProducido} unidades")
                ->send();

            // Si el total producido es igual o mayor a la cantidad a producir
            // desactivamos la fabricación
            if ($totalProducido >= $fabricacion->cantidad_a_producir) {
                $fabricacion->update([
                    'estado' => 0 
                ]);

                Notification::make()
                    ->title('Fabricación completada')
                    ->success()
                    ->body('La fabricación se ha desactivado automáticamente porque se alcanzó la cantidad objetivo.')
                    ->send();

                // Refrescar la página para mostrar los cambios
                $this->redirect(request()->header('Referer'));
            }
        }
    }
}
