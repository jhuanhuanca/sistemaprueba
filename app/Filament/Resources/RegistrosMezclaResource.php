<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrosMezclaResource\Pages;
use App\Filament\Resources\RegistrosMezclaResource\RelationManagers;
use App\Models\RegistrosMezcla;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistrosMezclaResource extends Resource 
{
    protected static ?string $model = RegistrosMezcla::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Registros de Mezcla';
    protected static ?string $modelLabel = 'Registro de Mezcla';
    protected static ?string $pluralModelLabel = 'Registros de Mezcla';
    protected static ?string $navigationGroup = 'REPROCESADOS/MESCLADOS';
    private static function calcularCostoMaquina($state, callable $set, callable $get): void
    {
        $maquina = \App\Models\Equipos::find($state);
        $maq = $maquina->consumoEnergetico??0;
        $set('costo_maquina', number_format(($maq/60)*5, 2, '.', '')??0);
    }
    private static function calcularCostoTotal($state, callable $set, callable $get): void
    {
        $horasTrabajadas = floatval($get('horas_trabajadas'));
        $manoObra = floatval($get('mano_obra'));
        $cantidadMezcladas = floatval($get('cantidad_mezcladas'));
        $costoMaquina = floatval($get('costo_maquina'));
        
        if ($horasTrabajadas && $manoObra && $cantidadMezcladas && $costoMaquina) {
            $costoTotal = ($horasTrabajadas * $manoObra) + ($cantidadMezcladas * $costoMaquina);
            $set('costo_total', number_format($costoTotal, 2, '.', ''));
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label(label: 'codigo')
                            ->default(state: fn (): string=>'Nro-'. random_int (min: 1000,max: 99999))
                            ->readOnly()
                            ->required(),
                        Forms\Components\Select::make('fabricacion_id')
                            ->label('Orden de Fabricación')
                            ->relationship(
                                name: 'fabricacion',
                                titleAttribute: 'codigo'
                            )
                            ->required()
                            ->default(fn () => \App\Models\Fabricacion::where('codigo', request('codigo'))->value('id')) 
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('mezcla_id')
                            ->label('Mezcla')
                            ->relationship('mezcla', 'codigo')
                            ->required()
                            ->default(fn () => request('mezcla_id'))
                            ->searchable(),
                    ])->columns(3),
                
                Forms\Components\Section::make('Recursos')
                    ->schema([
                        Forms\Components\Select::make('empleado_id')
                            ->label('Empleado')
                            ->relationship('empleado', 'nombres')
                            ->afterStateUpdated(function ($state, $set, $get) { 
                                $empleado = \App\Models\Empleados::find($state);
                                $set('mano_obra', $empleado->salario_hora??0);
                            })
                            ->searchable()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('equipo_id')
                            ->label('Equipo')
                            ->relationship('equipo', 'nombre')
                            ->afterStateUpdated(function ($state, $set, $get) {
                                self::calcularCostoMaquina($state, $set, $get);
                            })
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('cantidad_por_mezclar')
                            ->label('Cantidad a Mezclar')
                            ->required()
                            ->readOnly()
                            ->suffix('Mezclas')
                            ->default(fn () => request('cantidad_por_mezclar'))
                            ->numeric(),
                    ])->columns(3),

                Forms\Components\Section::make('Detalles de Producción')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->default(now()->format('Y-m-d'))
                            ->native(false)
                            ->required(),
                        Forms\Components\TimePicker::make('hora_inicio')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $horaInicio = $state;
                                $horaFin = $get('hora_fin');
                    
                                if ($horaInicio && $horaFin) {
                                    $horasTrabajadas = self::calcularHoras($horaInicio, $horaFin);
                                    $set('horas_trabajadas', $horasTrabajadas);
                                }
                            }),
                    
                        Forms\Components\TimePicker::make('hora_fin')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $horaFin = $state;
                                $horaInicio = $get('hora_inicio');
                    
                                if ($horaInicio && $horaFin) {
                                    $horasTrabajadas = self::calcularHoras($horaInicio, $horaFin);
                                    $set('horas_trabajadas', $horasTrabajadas);
                                }
                            }),
                        Forms\Components\TextInput::make('cantidad_mezcladas')
                            ->label('Cantidad Mezclada')
                            ->required()
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                self::calcularCostoTotal($state, $set, $get);
                            }),
                    ])->columns(4),

                Forms\Components\Section::make('Costos')
                    ->schema([
                        Forms\Components\TextInput::make('horas_trabajadas')
                            ->label('Horas Trabajadas')
                            ->required()
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                self::calcularCostoTotal($state, $set, $get);
                            })
                            ->readOnly(),
                        Forms\Components\TextInput::make('mano_obra')
                            ->label('Costo Mano de Obra')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                self::calcularCostoTotal($state, $set, $get);
                            })
                            ->numeric()
                            ->suffix('Bs/hr'),
                        Forms\Components\TextInput::make('costo_maquina')
                            ->label('Costo de Máquina')
                            ->required()
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                self::calcularCostoTotal($state, $set, $get);
                            })
                            ->suffix('KW/mezcla'),
                        Forms\Components\TextInput::make('costo_total')
                            ->label('Costo Total')
                            ->required()
                            ->numeric()
                            ->suffix('Bs.')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                self::calcularCostoTotal($state, $set, $get);
                            })
                            ->readOnly(),
                    ])->columns(4),

                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fabricacion.codigo')
                    ->label('Fabricación')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mezcla.codigo')
                    ->label('Mezcla')
                    ->sortable(),
                Tables\Columns\TextColumn::make('empleado.nombres')
                    ->label('Empleado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_mezcladas')
                    ->label('Cantidad Mezclada')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('costo_total')
                    ->label('Costo Total')
                    ->money('Bs.')
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    private static function calcularHoras($horaInicio, $horaFin)
    {
        $inicio = Carbon::parse($horaInicio);
        $salida = Carbon::parse($horaFin);
    
        if ($salida < $inicio) {
            $salida->addDay();
        }
    
        $diferencia = $salida->diffInHours($inicio) + ($salida->diffInMinutes($inicio) % 60) / 60;
        return number_format(abs($diferencia), 2);
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
            'index' => Pages\ListRegistrosMezclas::route('/'),
            'create' => Pages\CreateRegistrosMezcla::route('/create'),
            'edit' => Pages\EditRegistrosMezcla::route('/{record}/edit'),
        ];
    }
}
