<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReprocesadosResource\Pages;
use App\Filament\Resources\ReprocesadosResource\RelationManagers;
use App\Models\empleados;
use App\Models\Equipos;
use App\Models\Reprocesados;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReprocesadosResource extends Resource
{
    protected static ?string $model = Reprocesados::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'REPROCESADOS/MESCLADOS';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->default('MAT_REC-' . random_int(1000, 99999))
                            ->readOnly()
                            ->required(),
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('peso')
                            ->required()
                            ->live()
                            ->numeric()
                            ->suffix('Kg'),
                        Forms\Components\TextInput::make('color')
                            ->required(),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'molido' => 'Molido',
                                'lavado' => 'Lavado',
                                'peletizado' => 'Peletizado',
                                'uso final/almacenado' => 'Uso Final/Almacenado',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('Tiempo y Personal')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->required()
                            ->default(now())
                            ->format('Y-m-d'),
                        Forms\Components\TimePicker::make('hora_inicio')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $horaInicio = $state;
                                $horaSalida = $get('hora_salida');
                    
                                if ($horaInicio && $horaSalida) {
                                    $horasTrabajadas = self::calcularHoras($horaInicio, $horaSalida);
                                    $set('horas_trabajadas', $horasTrabajadas);
                                }
                            }),    
                        Forms\Components\TimePicker::make('hora_salida')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $horaSalida = $state;
                                $horaInicio = $get('hora_inicio');
                    
                                if ($horaInicio && $horaSalida) {
                                    $horasTrabajadas = self::calcularHoras($horaInicio, $horaSalida);
                                    $set('horas_trabajadas', $horasTrabajadas);
                                }
                            }), 
                        Forms\Components\TextInput::make('horas_trabajadas')
                            ->required()
                            ->numeric()
                            ->suffix('hrs')
                            ->live()
                            ->readOnly(),
                        Forms\Components\Select::make('equipo_id')
                            ->relationship('equipos', 'nombre')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $equipo = Equipos::find($state);
                                    if ($equipo) {
                                        $set('costoMaq', $equipo->consumoEnergetico);
                                        self::calculateCosts($set, $get);
                                    }
                                }
                            }),
                        Forms\Components\Select::make('empleado_id')
                            ->relationship('empleados', 'nombres')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $empleado = empleados::find($state);
                                    if ($empleado) {
                                        $set('costoEmp', $empleado->salario_hora);
                                        self::calculateCosts($set, $get);
                                    }
                                }
                            }),
                            Forms\Components\TextInput::make('costoMaq')
                            ->label('Costo de Maquinaria')
                            ->required()
                            ->numeric()
                            ->suffix('KW/hr')
                            ->readOnly(),
                        Forms\Components\TextInput::make('costoEmp')
                            ->label('Costo por Empleado')
                            ->required()
                            ->numeric()
                            ->suffix('$Bs./hr')
                            ->readOnly(),
                    ])->columns(2),

                Forms\Components\Section::make('Información de Costos')
                    ->schema([
                        Forms\Components\TextInput::make('costoextra')
                            ->label('Costos Extras')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('$Bs.')
                            ->live()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::calculateCosts($set, $get)),
                        Forms\Components\TextInput::make('costoManoObra')
                            ->label('Costo Mano de Obra')
                            ->required()
                            ->numeric()
                            ->suffix('$Bs.')
                            ->readOnly()
                            ->default(0)
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('otroscostos')
                            ->label('Costo Consumo de Energia ')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('$Bs.')
                            ->readOnly()
                            ->dehydrated(true),
                            Forms\Components\TextInput::make('costokilo')
                            ->label('Costo por Kilo')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->suffix('$Bs./kg')
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('costoTotal')
                            ->label('Costo Total')
                            ->required()
                            ->numeric()
                            ->suffix('$Bs.')
                            ->readOnly()
                            ->default(0)
                            ->dehydrated(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peso')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReprocesados::route('/'),
            'create' => Pages\CreateReprocesados::route('/create'),
            'edit' => Pages\EditReprocesados::route('/{record}/edit'),
        ];
    }

    private static function calcularHoras($horaInicio, $horaSalida): float
    {
        $inicio = \Carbon\Carbon::parse($horaInicio);
        $salida = \Carbon\Carbon::parse($horaSalida);
        
        if ($salida < $inicio) {
            $salida->addDay(); // Si la salida es al día siguiente
        }
        
        return round($inicio->floatDiffInHours($salida), 2);
    }

    private static function calculateCosts(callable $set, callable $get): void
    {
        $costoEmp = floatval($get('costoEmp') ?? 0);
        $costoMaq = floatval($get('costoMaq') ?? 0);
        $horasTrabajadas = floatval($get('horas_trabajadas') ?? 0);
        $costoextra = floatval($get('costoextra') ?? 0);
        $peso = floatval($get('peso') ?? 0);

        // Calcular costo mano de obra
        $costoManoObra = round($costoEmp * $horasTrabajadas, 2);
        $set('costoManoObra', $costoManoObra);

        // Calcular otros costos (consumo energético)
        $otrosCostos = round(($costoMaq * $horasTrabajadas) * 0.68, 2);
        $set('otroscostos', $otrosCostos);

        // Calcular costo total
        $costoTotal = $costoManoObra + $otrosCostos + $costoextra;
        $set('costoTotal', round($costoTotal, 2));

        // Calcular costo por kilo 
        if ($costoTotal > 0) {
            $costoKilo = round( $peso /$costoTotal , 2);
            $set('costokilo', $costoKilo);
        }
    }
}
