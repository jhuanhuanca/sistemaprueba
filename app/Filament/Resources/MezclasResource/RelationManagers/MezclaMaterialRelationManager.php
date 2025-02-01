<?php

namespace App\Filament\Resources\MezclasResource\RelationManagers;

use Illuminate\Support\Facades\Log;
use App\Models\Insumos;
use App\Models\Reprocesados;
use Doctrine\DBAL\Query\From;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Reactive;
use App\Filament\Resources\MezclasResource;
use Closure;
use Filament\Notifications\Notification;

if (!function_exists('get_request_form_data')) {
    function get_request_form_data($key) {
        return request()->input('data.' . $key);
    }
}

class MezclaMaterialRelationManager extends RelationManager
{
    protected static string $relationship = 'MezclaMaterial'; 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo_material')
                    ->label('Seleccionar tipo')
                    ->options([
                        'insumo' => 'Insumo',
                        'reprocesado' => 'Reprocesado'
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        // Limpiar los campos cuando cambia la selección
                        $set('insumo_id', null);
                        $set('reprocesados_id', null);
                        $set('codigo', null);
                        $set('tipo', null);
                        $set('color', null);
                        $set('costo_kilo', null);
                        $set('costokilo', null);
                        $set('costo_total', null);
                    })
                    ->native(false),
                Forms\Components\Select::make('insumo_id')
                    ->label('Insumo')
                    ->relationship('insumos', 'nombre')
                    ->required(fn (Forms\Get $get) => $get('tipo_material') === 'insumo')
                    ->visible(fn (Forms\Get $get) => $get('tipo_material') === 'insumo')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        
                        
                            // Forzamos la evaluación del tipo
                            $tipoInsumo =  null;
                        if ($state && $insumo = Insumos::find($state)) {
                            $set('codigo', $insumo->codigo);
                            $tipoInsumo = strtolower(trim($insumo->tipo));
                            if ($tipoInsumo === 'virgen') {
                                $set('tipo', 'material virgen');
                            } else {
                                $set('tipo', 'material reciclado');
                            }
                            $set('color', $insumo->color);
                            $set('costo_kilo', $insumo->costo_kilo);
                            
                            // Recalcular costo total si hay cantidad
                            if ($cantidad = $get('cantidad')) {
                                $set('costo_total', $cantidad * $insumo->costo_kilo);
                            }
                        }
                    }),
                Forms\Components\Select::make('reprocesados_id')
                    ->label('Reprocesado')
                    ->relationship('reprocesados', 'descripcion')
                    ->required(fn (Forms\Get $get) => $get('tipo_material') === 'reprocesado')
                    ->visible(fn (Forms\Get $get) => $get('tipo_material') === 'reprocesado')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        if ($state && $reprocesado = Reprocesados::find($state)) {
                            $set('color', $reprocesado->color);
                            $set('codigo', $reprocesado->codigo);
                            $set('tipo', 'material reciclado');
                            $set('costo_kilo', $reprocesado->costokilo);
                            // Recalcular costo total si hay cantidad
                            if ($cantidad = $get('cantidad')) {
                                $set('costo_total', $cantidad * $reprocesado->costokilo);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('codigo')
                    ->live()
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('tipo')
                    ->label('Tipo')
                    ->live(onBlur: true)
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('cantidad')
                    ->label('peso')
                    ->numeric()
                    ->live(onBlur: true)
                    ->required()
                    ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                        $tipoMaterial = $get('tipo_material');
                        $stockDisponible = 0;

                        if ($tipoMaterial === 'insumo' && $insumoId = $get('insumo_id')) {
                            $insumo = Insumos::find($insumoId);
                            $stockDisponible = $insumo ? $insumo->stock : 0;
                        } elseif ($tipoMaterial === 'reprocesado' && $reprocesadoId = $get('reprocesados_id')) {
                            $reprocesado = Reprocesados::find($reprocesadoId);
                            $stockDisponible = $reprocesado ? $reprocesado->peso : 0;
                        }

                        if ($state > $stockDisponible) {
                            Notification::make()
                                ->title('Stock Insuficiente')
                                ->body("Stock disponible: {$stockDisponible} Kg")
                                ->danger()
                                ->persistent()
                                ->send();

                            // Esto hará que el formulario no se envíe
                            throw new \Exception('Stock insuficiente');
                        }

                        return $state;
                    })
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        // Validar stock disponible
                        $tipoMaterial = $get('tipo_material');
                        $stockDisponible = 0;
                        
                        if ($tipoMaterial === 'insumo' && $insumoId = $get('insumo_id')) {
                            $insumo = Insumos::find($insumoId);
                            $stockDisponible = $insumo ? $insumo->stock : 0;
                        } elseif ($tipoMaterial === 'reprocesado' && $reprocesadoId = $get('reprocesados_id')) {
                            $reprocesado = Reprocesados::find($reprocesadoId);
                            $stockDisponible = $reprocesado ? $reprocesado->peso : 0;
                        }

                        // Si la cantidad solicitada es mayor al stock, mostrar alerta
                        if ($state > $stockDisponible) {
                            Notification::make()
                                ->title('Stock Insuficiente')
                                ->body("Stock disponible: {$stockDisponible} Kg")
                                ->danger()
                                ->persistent()
                                ->send();
                            
                            $set('cantidad', null);
                            return;
                        }

                        

                        // Continuar con el resto de los cálculos
                        $costoKilo = $get('costo_kilo') ?? 0;
                        $cantidad = $state ?? 0;
                        $set('costo_total', $cantidad * $costoKilo);
                        
                        // Actualizar kilos utilizados y porcentajes
                        $mezclasController = new \App\Http\Controllers\MezclasController();
                        
                        // Forzar actualización del registro padre
                        $this->ownerRecord->refresh();
                        
                        // Calcular y actualizar kilos utilizados
                        $totalKilos = $this->ownerRecord->mezclaMaterial->sum('cantidad');
                        $this->ownerRecord->kilos_utilizados = $totalKilos;
                        $this->ownerRecord->save();
                        
                        // Calcular porcentajes
                        $mezclasController->calculatePorcentajes($this->ownerRecord->id);
                        
                        // Forzar la actualización de la vista
                        $this->getOwnerRecord()->refresh();
                        
                        // Forzar la actualización de los campos en el formulario
                        if ($this->getOwnerRecord()) {
                            $set('reciclado', $this->getOwnerRecord()->reciclado);
                            $set('virgen', $this->getOwnerRecord()->virgen);
                        }
                    })
                    ->suffix('Kg.')
                    ->extraInputAttributes(fn (Forms\Get $get) => [
                        'style' => $get('cantidad_error') ? 'border-color: red;' : null,
                    ])
                    ->helperText(fn (Forms\Get $get) => $get('cantidad_error')),
                Forms\Components\TextInput::make('color')
                    ->required()
                    ->live()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('costo_kilo')
                    ->label('Costo por Kilo')
                    ->numeric()
                    ->readonly()
                    ->live()
                    ->suffix('Bs.'),
                Forms\Components\TextInput::make('costo_total')
                    ->label('Costo Total')
                    ->numeric()
                    ->live()
                    ->readonly()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $this->ownerRecord->refresh();
                        
                        // Actualizar costo_mezcla
                        $totalCostos = $this->ownerRecord->mezclaMaterial->sum('costo_total');
                        $this->ownerRecord->costo_mezcla = $totalCostos;
                        
                        // Actualizar costo_total (suma de costo_mezcla y costo_master)
                        $this->ownerRecord->costo_total = $totalCostos + ($this->ownerRecord->costo_master ?? 0);
                        
                        $this->ownerRecord->save();
                        $this->getOwnerRecord()->refresh();
                    })
                    ->suffix('Bs.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_material')
                    ->label('Material')
                    ->formatStateUsing(function ($record) {
                        return match($record->tipo_material) {
                            'insumo' => $record->insumos?->nombre ?? '-',
                            'reprocesado' => $record->reprocesados?->descripcion ?? '-',
                            default => '-'
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('tipo de material')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('peso')
                    ->suffix('Kg')
                    ->searchable(),   
                Tables\Columns\TextColumn::make('costo_total')
                    ->money(' Bs')
                    ->searchable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['insumos', 'reprocesados']));
    }

    protected function handleRecordCreation(array $data): mixed
    {
        // Actualizar stock antes de crear el registro
        $this->actualizarStock($data);
        
        return parent::handleRecordCreation($data);
    }

    protected function handleRecordUpdate(mixed $record, array $data): mixed
    {
        // Restaurar stock anterior
        if ($record->tipo_material === 'insumo' && $record->insumo_id) {
            $insumo = Insumos::find($record->insumo_id);
            if ($insumo) {
                $insumo->stock += $record->cantidad;
                $insumo->save();
            }
        } elseif ($record->tipo_material === 'reprocesado' && $record->reprocesados_id) {
            $reprocesado = Reprocesados::find($record->reprocesados_id);
            if ($reprocesado) {
                $reprocesado->stock += $record->cantidad;
                $reprocesado->save();
            }
        }

        // Actualizar con nuevo stock
        $this->actualizarStock($data);
        
        return parent::handleRecordUpdate($record, $data);
    }

    protected function handleRecordDeletion(mixed $record): void
    {
        // Restaurar stock al eliminar
        if ($record->tipo_material === 'insumo' && $record->insumo_id) {
            $insumo = Insumos::find($record->insumo_id);
            if ($insumo) {
                $insumo->stock += $record->cantidad;
                $insumo->save();
            }
        } elseif ($record->tipo_material === 'reprocesado' && $record->reprocesados_id) {
            $reprocesado = Reprocesados::find($record->reprocesados_id);
            if ($reprocesado) {
                $reprocesado->stock += $record->cantidad;
                $reprocesado->save();
            }
        }
        
        parent::handleRecordDeletion($record);
    }

    private function actualizarStock(array $data): void
    {
        if ($data['tipo_material'] === 'insumo' && isset($data['insumo_id'])) {
            $insumo = Insumos::find($data['insumo_id']);
            if ($insumo) {
                $insumo->stock -= $data['cantidad'];
                $insumo->save();
            }
        } elseif ($data['tipo_material'] === 'reprocesado' && isset($data['reprocesados_id'])) {
            $reprocesado = Reprocesados::find($data['reprocesados_id']);
            if ($reprocesado) {
                $reprocesado->stock -= $data['cantidad'];
                $reprocesado->save();
            }
        }
    }
}
