<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduccionDiariaResource\Pages;
use App\Filament\Resources\ProduccionDiariaResource\RelationManagers;
use App\Models\ProduccionDiaria;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProduccionDiariaResource extends Resource
{
    protected static ?string $model = ProduccionDiaria::class;

    protected static ?string $navigationIcon = 'heroicon-o-Document';
    protected static ?string $navigationLabel = 'Produccion Diaria';
    protected static ?string $modelLabel = 'Producciones diarias';
    protected static ?string $navigationGroup = 'PLANIFICACION DE PRODUCCION';
    protected static ?int $navigationSort = 6;

    public $codigo;

    public function mount(): void
    {
        $this->codigo = request('codigo'); // Inicializar la propiedad con el valor de la URL
    }
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Wizard::make(steps: [
                Forms\Components\Wizard\Step::make(label: 'Detalles de Orden')
                ->schema(components: [
                        Forms\Components\TextInput::make('numero_lote')
                            ->label(label: 'codigo')
                            ->default(state: fn (): string=>'Nro-'. random_int (min: 1000,max: 999999))
                            ->readOnly()
                            ->required(),
                        Forms\Components\Select::make('fabricacion_id')
                        ->relationship('Fabricacion', 'codigo') // Relación con el modelo Fabricacion
                        ->label('Orden de Producción')
                        ->default(fn () => \App\Models\Fabricacion::where('codigo', request('codigo'))->value('id')) // Usar request directamente
                        ->searchable(),
                           // ->required(),
                        Forms\Components\TextInput::make('usuario')
                            ->label(label: 'Operador')
                            ->default(state: fn (): mixed => Auth::user()->name)
                            ->readOnly()
                            ->required(),
                        Forms\Components\TextInput::make('producto')
                        ->default(fn () => \App\Models\Fabricacion::where('producto', request('producto'))->value('producto')), // Usar request directamente,
                        Forms\Components\TextInput::make('equipo_id')
                        ->default(fn () => \App\Models\Fabricacion::where('equipo_id', request('equipo_id'))->value('equipo_id'))
                            ->label('maquina'),
                ])->columns(columns: 2),
            Forms\Components\Wizard\Step::make(label: 'fecha y horario de trabajo')
                ->schema(components: [
                        Forms\Components\DatePicker::make('fecha')
                            ->default(state: now())
                            ->readOnly()
                            ->required(),
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
                            ->readOnly()
                            ->live()
                ])->columns(columns: 2),
            Forms\Components\Wizard\Step::make(label: 'Detalles de produccion')
                ->schema(components: [
                        Forms\Components\TextInput::make('cantidad_producida')
                            ->required()
                            ->numeric()
                            ->suffix('Unds.'),
                        Forms\Components\TextInput::make('PAQ'),
                        Forms\Components\TextInput::make('Cantidad por Paq'),
                        Forms\Components\TextInput::make('color')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('desperdicios')
                            ->required()
                            ->numeric()
                            ->suffix('Unds.'),
                        Forms\Components\Textarea::make('observaciones')
                            ->columnSpanFull(),
                ])->columns(columns: 2),
            ])->columnSpanFull(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') 
            ->columns([
                Tables\Columns\TextColumn::make('Fabricacion.codigo')->label('fabricacion')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('fecha')
                        ->date()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('hora_inicio')
                    ->time('H:i') 
                    ->label('Entrada')
                    ->alignCenter()
                    ->sortable(),
                
                    Tables\Columns\TextColumn::make('hora_salida')
                    ->time('H:i') 
                    ->label('Salida')
                    ->alignCenter()
                    ->sortable(),
                
                    Tables\Columns\TextColumn::make('horas_trabajadas')
                        ->sortable()
                        ->label('Horas')
                        ->alignCenter()
                        ->suffix('    Hrs'),
                    Tables\Columns\TextColumn::make('numero_lote')
                    ->label('lote')
                        ->searchable(),
                        Tables\Columns\TextColumn::make('producto')
                        ->label('Producto')
                        ->getStateUsing(function () {
                            return \App\Models\Fabricacion::where('producto', request('productos'))->value('producto');
                        })
                        ->sortable(),
                    Tables\Columns\TextColumn::make('cantidad_producida')
                        ->numeric()
                        ->label('Cantidad')
                        ->alignCenter()
                        ->suffix(' unids.')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('usuario')
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
                Tables\Filters\Filter::make('Día')
                    ->form([
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Seleccionar Día')
                            ->default(now()->toDateString()), // Por defecto, la fecha de hoy
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when(
                                $data['fecha'] ?? null,
                                fn($query, $fecha) => $query->whereDate('fecha', $fecha)
                            );
                    })
                    ->default(), // Por defecto, muestra los registros de hoy
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalles de Fabricación')
                    ->modalContent(fn ($record) => view('modals.ver-fabricacion', ['fabricacion' => $record]))
                    ->modalWidth('lg'),//
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
    private static function calcularHoras($horaInicio, $horaSalida)
{
    $inicio = Carbon::parse($horaInicio);
    $salida = Carbon::parse($horaSalida);

    // Si la hora de salida es menor que la hora de inicio, asumimos que cruza la medianoche
    if ($salida < $inicio) {
        $salida->addDay(); // Sumar un día para que continúe correctamente
    }

    $diferencia = $salida->diffInHours($inicio) + ($salida->diffInMinutes($inicio) % 60) / 60; // En formato decimal
    return number_format(abs($diferencia), 2); // Ajustar la cantidad de decimales si es necesario
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduccionDiarias::route('/'),
            'create' => Pages\CreateProduccionDiaria::route('/create'),
            'edit' => Pages\EditProduccionDiaria::route('/{record}/edit'),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
