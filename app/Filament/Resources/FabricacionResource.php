<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FabricacionResource\Pages;
use App\Filament\Resources\FabricacionResource\RelationManagers\ProcesosRelationManager;
use App\Models\empleados;
use App\Models\Equipos;
use App\Models\Fabricacion;
use App\Models\Mezclas;
use App\Models\OrdenProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;

class FabricacionResource extends Resource
{
    protected static ?string $model = Fabricacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Orden de Fabricacion';
    protected static ?string $modelLabel = 'Fabricacion';
    protected static ?string $navigationGroup = 'PLANIFICACION DE PRODUCCION';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información de Fabricación')
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->default('FAB-' . random_int(1000, 99999))
                        ->readOnly() 
                        ->required(),
                    Forms\Components\Select::make('orden_produccion_id')
                        ->relationship('ordenProduccion', 'codigo')
                        ->searchable()
                        ->placeholder('Selecciona una orden de producción')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $orden = OrdenProduccion::find($state);
                            $set('area', $orden?->areas?->descripcion ?? 'N/A');  
                            $set('producto', $orden?->productos?->nombre ?? 'N/A');
                            $set('cantidad_a_producir', $orden?->cantidad_producir ?? 0);
                        })
                        ->required(),
                    Forms\Components\TextInput::make('producto')
                        ->label('Producto')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            self::calculateMezclas($state, $set, $get);
                        }),
                    Forms\Components\TextInput::make('area')
                        ->readOnly()
                        ->required(),
                        Forms\Components\TextInput::make('usuario')
                        ->required()
                        ->readOnly()
                        ->default(Auth::user()->name),
                ])->columns(2),

            Section::make('Detalles de Producción')
                ->schema([
                    Forms\Components\Select::make('empleado_id')
                    ->options(empleados::pluck('nombres', 'id'))
                    ->label('Operario Asignado')
                    ->searchable(),
                    Forms\Components\Select::make('equipo_id')
                    ->options(function() {
                        return Equipos::all()->mapWithKeys(function ($equipo) {
                            return [$equipo->id => $equipo->nombre];
                        });
                    })
                    ->label('Maquinaria')
                    ->searchable() 
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state) {
                            $equipo = Equipos::find($state);
                            if ($equipo) {
                                $set('costo_maq', number_format($equipo->costoMaq, 2, '.', ''));
                            }
                        }
                    }),
                    ///////////
                    Forms\Components\Select::make('mezcla_id')
                    ->relationship(
                        name: 'mezcla',
                        titleAttribute: 'codigo',
                        modifyQueryUsing: fn ($query) => $query->latest('fecha')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "{$record->codigo} - {$record->tipo} - " . 
                        \Carbon\Carbon::parse($record->fecha)->format('d/m/Y H:i')
                    )
                    ->label('Mezcla')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state) {
                            $mezcla = Mezclas::find($state);
                            if ($mezcla) {
                                $set('costo_mezcla', $mezcla->costo_total ?? 0);
                                $set('tipo_material', $mezcla->tipo ?? '');
                                
                                // Llamar al método calculateMezclas para actualizar cantidad_mezclas
                                self::calculateMezclas($state, $set, $get);
                            }
                        } else {
                            $set('costo_mezcla', 0);
                            $set('tipo_material', '');
                            $set('cantidad_mezclas', 0);
                        }
                    })
                    ->suffixAction(
                        Action::make('nuevo mezcla')
                            ->label('añadir mezcla')
                            ->button()
                            ->color('primary')
                            ->modalHeading('añadir mezcla')
                            ->form([
                                Section::make('Información General')
                                    ->schema([
                                        Forms\Components\TextInput::make('codigo')
                                            ->default('MEZ-'. random_int(100,999))
                                            ->readOnly()
                                            ->required(),
                                        Forms\Components\DateTimePicker::make('fecha')
                                            ->default(now())
                                            ->disabled()
                                            ->required(),
                                        Forms\Components\TextInput::make('usuario')
                                            ->label('Encargado')
                                            ->default(fn(): mixed => Auth::user()->name)
                                            ->readOnly()
                                            ->required(),
                                        Forms\Components\Toggle::make('estado')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->label('Estado de la Mezcla'),
                                    ])->columns(2),
                                Section::make('Detalles de la Mezcla')
                                    ->schema([
                                        Forms\Components\select::make('master_id')
                                            ->relationship('masters', 'codigo')
                                            ->default(function() {
                                                return \App\Models\Masters::first()?->id;
                                            })
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, $get, $set) {
                                                MezclasResource::calculateMasterCosts($state, $set, $get);
                                            }),
                                        Forms\Components\TextInput::make('peso_master')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('gr')
                                            ->live()
                                            ->afterStateUpdated(function ($state, $get, $set) {
                                                MezclasResource::calculateMasterCosts($state, $set, $get);
                                            }),
                                        Forms\Components\TextInput::make('kilos_utilizados')
                                            ->numeric()
                                            ->live()
                                            ->default(0)
                                            ->suffix('Kg'),
                                        Forms\Components\Select::make('tipo')
                                            ->label('Tipo de Material')
                                            ->options([
                                                'material virgen' => 'Plastico Virgen',
                                                'material reciclado' => 'Plastico Reciclado',
                                                'virgen/reciclado' => 'Virgen/Reciclado'
                                            ])
                                            ->native(false)
                                            ->required(),
                                    ])->columns(2),

                                // Sección de Costos
                                Section::make('Costos')
                                    ->schema([
                                        Forms\Components\TextInput::make('costo_master')
                                            ->numeric()
                                            ->suffix('Bs/gr')
                                            ->readOnly()
                                            ->default(0)
                                            ->live(),
                                        Forms\Components\TextInput::make('costo_mezcla')
                                            ->label('Costo de Mezcla')
                                            ->prefix('$')
                                            ->readOnly()
                                            ->default(0)
                                            ->dehydrated()
                                            ->live(onBlur: true)
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->step('0.01')
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $mezcla = Mezclas::find($get('mezcla_id'));
                                                if ($mezcla) {
                                                    $set('costo_mezcla', $mezcla->costo_total ?? 0);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('costo_total')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->label('posible costo')
                                            ->prefix('$Bs')
                                            ->disabled()
                                            ->dehydrated()
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $cantidadMezclas = floatval($get('cantidad_mezclas') ?? 0);
                                                $costoMezcla = floatval($get('costo_mezcla') ?? 0);
                                                $costoTotal = $cantidadMezclas * $costoMezcla;
                                                $set('costo_total', $costoTotal);
                                            }),
                                    ])->columns(3),

                                // Sección de Cantidades
                                Section::make('Cantidades')
                                    ->schema([
                                        Forms\Components\TextInput::make('virgen')
                                            ->label('Total M.Virgen')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->readOnly()
                                            ->live()
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('reciclado')
                                            ->label('Total M.Reciclado')
                                            ->numeric()
                                            ->default(0)
                                            ->readOnly()
                                            ->live()
                                            ->suffix('%'),
                                    ])->columns(2),

                                Forms\Components\Textarea::make('observaciones')
                                    ->columnSpanFull(),
                            ])
                            ->action(function (array $data, callable $set) {
                                $mezcla = Mezclas::create($data);
                                $set('mezcla_id', $mezcla->id);

                                Notification::make()
                                    ->title('Mezcla creada con éxito')
                                    ->success()
                                    ->send();
                            })
                    ),
                        /////////////////////
                    Forms\Components\Select::make('tipo_material')
                        ->label('Tipo de Material')
                        ->options([
                            'material virgen' => 'Plástico Virgen',
                            'material reciclado' => 'Plástico Reciclado',
                            'virgen/reciclado' => 'Virgen/Reciclado'
                        ])
                        ->required(),
                ])->columns(2),

            Section::make('Fechas y Cantidades')
                ->schema([
                    Forms\Components\DateTimePicker::make('fecha_inicio')
                        ->required()
                        ->label('Fecha de Inicio'),
                    Forms\Components\DateTimePicker::make('fecha_finalizacion')
                        ->required()
                        ->label('Fecha de Finalización'),
                    Forms\Components\TextInput::make('cantidad_a_producir')
                        ->required()
                        ->label('Cantidad de Producción')
                        ->default(0)
                        ->numeric()
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            // Llamar al método calculateMezclas cuando cambie la cantidad
                            self::calculateMezclas($state, $set, $get);
                        }),
                    Forms\Components\TextInput::make('cantidad_mezclas')
                        ->required()
                        ->live(onBlur: true)
                        ->label('Cantidad de Mezclas')
                        ->numeric(),
                ])->columns(3),

            Section::make('Costos')
                ->schema([
                    Forms\Components\TextInput::make('costo_maq')
                        ->label('Costo de Maquinaria')
                        ->prefix('$')
                        ->numeric()
                        ->readOnly()
                        ->dehydrated(true)
                        ->default(function ($record) {
                            if ($record && $record->equipo_id) {
                                return number_format($record->equipo->costoMaq ?? 0, 2, '.', '');
                            }
                            return '0.00';
                        }),
                    Forms\Components\TextInput::make('costo_mezcla')
                        ->label('Costo de Mezcla')
                        ->prefix('$')
                        ->readOnly()
                        ->dehydrated()
                        ->live()
                        ->default(0)
                        ->numeric()
                        ->inputMode('decimal')
                        ->step('0.01'),
                    Forms\Components\TextInput::make('costo_total')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->label('Costo Total')
                        ->prefix('$Bs')
                        ->readOnly()
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $cantidadMezclas = floatval($get('cantidad_mezclas') ?? 0);
                            $costoMezcla = floatval($get('costo_mezcla') ?? 0);
                            
                            // Calcular el costo total sumando el costo de las mezclas y el costo de maquinaria
                            $costoTotal = $cantidadMezclas * $costoMezcla;
                           
                            
                            $set('costo_total', number_format($costoTotal, 2, '.', ''));
                        }),
                    Forms\Components\Select::make('estado')
                        ->options([
                            1 => 'Activo',
                            0 => 'Inactivo'
                        ])
                        ->default(0)
                        ->label('Estado de la Orden')
                        ->native(false),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordenProduccion.codigo')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->searchable(),
                Tables\Columns\TextColumn::make('producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad_mezclas')
                    ->label('Cantidad de Mezclas')
                    ->numeric(),
                Tables\Columns\TextColumn::make('cantidad_a_producir')
                    ->label('Cantidad a Producir')
                    ->numeric(),
                Tables\Columns\ToggleColumn::make('estado')
                    ->label('Estado'),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->label('Área')
                    ->relationship('ordenProduccion.areas', 'descripcion')
                    ->multiple()
                    ->preload()
                    ->indicator('Área seleccionada'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Fecha de Creación')
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['created_at'])) {
                            $query->whereDate('created_at', $data['created_at']);
                        }
                    })
                    ->label('Filtrar por Fecha')
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            
            ->actions([
                Tables\Actions\Action::make('crear_mezcla')
                    ->label('crear mezcla')
                    ->icon('heroicon-o-beaker')
                    ->url(function ($record) {
                        // Si ya tiene una mezcla asignada, redirigir a su edición
                        if ($record->mezcla_id) {
                            return route('filament.Dashboard.resources.mezclas.edit', ['record' => $record->mezcla_id]);
                        }
                        // Si no tiene mezcla, redirigir a crear nueva
                        return route('filament.Dashboard.resources.mezclas.create', ['fabricacion_id' => $record->id]);
                    })
                    ->after(function ($record) {
                        if (!$record->mezcla_id) {
                            // Solo ejecutar si es una nueva mezcla
                            $ultimaMezcla = Mezclas::latest()->first();
                            
                            if ($ultimaMezcla) {
                                $record->update([
                                    'mezcla_id' => $ultimaMezcla->id
                                ]);

                                Notification::make()
                                    ->title('Mezcla vinculada correctamente')
                                    ->success()
                                    ->send();

                                return redirect()->route('filament.Dashboard.resources.mezclas.edit', ['record' => $ultimaMezcla->id]);
                            }
                        }
                    }),
                Tables\Actions\Action::make('ver')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalles de Fabricación')
                    ->modalContent(fn ($record) => view('modals.ver-fabricacion', ['fabricacion' => $record]))
                    ->modalWidth('lg'),
                
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash'),
                    
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->striped();
    }
    public static function getRelations(): array
    {
        return [
            ProcesosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFabricacions::route('/'),
            'create' => Pages\CreateFabricacion::route('/create'),
            'edit' => Pages\EditFabricacion::route('/{record}/edit'),
        ];
    }

    protected static function calculateMezclas($state, $set, $get)
    {
        $cantidadProducir = floatval($get('cantidad_a_producir') ?? 0);
        $mezclaId = $get('mezcla_id');
        $productoId = $get('producto_id');
        
        if (!$mezclaId || !$productoId || !$cantidadProducir) {
            $set('cantidad_mezclas', 0);
            $set('costo_total', 0);
            return;
        }

        $mezcla = Mezclas::with('mezclaMaterial')->find($mezclaId);
        $producto = \App\Models\Productos::find($productoId);

        if (!$mezcla || !$producto) {
            $set('cantidad_mezclas', 0);
            $set('costo_total', 0);
            return;
        }

        // Obtener el peso unitario del producto en gramos
        $pesoProducto = floatval($producto->peso_unitario ?? 0);
        
        // Obtener los kilos totales de la mezcla sumando las cantidades de mezclaMaterial
        $kilosMezcla = $mezcla->mezclaMaterial->sum('cantidad');

        if ($pesoProducto > 0 && $kilosMezcla > 0) {
            $pesoTotalRequeridoKg = ($cantidadProducir * $pesoProducto) / 1000;
            $cantidadMezclas = ceil($pesoTotalRequeridoKg / $kilosMezcla);
            
            $set('cantidad_mezclas', $cantidadMezclas);
            
            // Recalcular el costo total
            $costoMezcla = floatval($get('costo_mezcla') ?? 0);
            $costoMaquinaria = floatval($get('costo_maq') ?? 0);
            $costoTotalMezclas = $cantidadMezclas * $costoMezcla;
            $costoTotal = $costoTotalMezclas + $costoMaquinaria;
            
            $set('costo_total', number_format($costoTotal, 2, '.', ''));
        } else {
            $set('cantidad_mezclas', 0);
            $set('costo_total', 0);
        }
    }
}