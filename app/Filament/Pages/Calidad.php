<?php

namespace App\Filament\Pages;

use App\Models\ControlProduccion;
use App\Models\ProduccionDiaria;
use App\Models\ProduccionFinal;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class Calidad extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.calidad';
    protected static ?string $title = 'Producciones Para Control de Calidad';
    protected static ?string $navigationGroup = 'CALIDAD Y CONTROL';

    /**
     * Configuración de la tabla.
     */
    protected function table(Table $table): Table
    {
        return $table
            ->query(ProduccionDiaria::query()->where('registrado', false))
            ->columns([
                Tables\Columns\TextColumn::make('Fabricacion.codigo')
                    ->label('Fabricación')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_lote')
                    ->label('Lote')
                    ->searchable(),
                Tables\Columns\TextColumn::make('producto')
                    ->label('Producto')
                    ->getStateUsing(function ($record) {
                        return $record->Fabricacion->producto ?? '-';
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
            ])
            ->actions([
                Tables\Actions\Action::make('registrar')
                    ->label('Aceptar Producción')
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading('Control de Producción')
                    ->form([
                        DatePicker::make('fecha')
                            ->default(now())
                            ->required(),
                        TextInput::make('numero_lote')
                            ->label('Número de Lote')
                            ->default(fn ($record) => $record->numero_lote)
                            ->readOnly(),
                        TextInput::make('nombre_operario')
                            ->required()
                            ->default(fn ($record) => $record->usuario)
                            ->maxLength(255),
                        TextInput::make('usuario')
                            ->label('Usuario')
                            ->default(Auth::user()->name)
                            ->readOnly(),
                        TextInput::make('produccion_total')
                            ->label('Producción Total')
                            ->default(fn ($record) => $record->cantidad_producida)
                            ->readOnly()
                            ->numeric()
                            ->suffix('Unds.'),
                        TextInput::make('produccion_rechazada')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('Unds.')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $produccionTotal = (float) $get('produccion_total');
                                $rechazada = (float) $state;
                                $set('produccion_aceptada', max(0, $produccionTotal - $rechazada));
                            }),
                        TextInput::make('produccion_aceptada')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->default(fn ($record) => $record->cantidad_producida)
                            ->suffix('Unds.'),
                        Select::make('resultado')
                            ->required()
                            ->options([
                                'malo' => 'Malo',
                                'regular' => 'Regular',
                                'bueno' => 'Bueno',
                            ])
                            ->native(false),
                        Textarea::make('defectos_encontrados')
                            ->columnSpanFull(),
                        Textarea::make('observaciones')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, $record) {
                        try {
                            // Crear el registro de Control de Producción
                            ControlProduccion::create([
                                'fecha' => $data['fecha'],
                                'produccion_diaria_id' => $record->id,
                                'nombre_operario' => $data['nombre_operario'],
                                'usuario' => $data['usuario'],
                                'produccion_rechazada' => $data['produccion_rechazada'],
                                'produccion_aceptada' => $data['produccion_aceptada'],
                                'resultado' => $data['resultado'],
                                'defectos_encontrados' => $data['defectos_encontrados'] ?? null,
                                'observaciones' => $data['observaciones'] ?? null,
                            ]);

                            // Actualizar ProduccionDiaria
                            $record->update([
                                'cantidad_producida' => $data['produccion_aceptada'],
                                'registrado' => true
                            ]);

                            // Crear registro en ProduccionFinal
                            ProduccionFinal::create([
                                'fabricacion_id' => $record->fabricacion_id,
                                'produccion_diaria_id' => $record->id,
                                'cantidad' => $data['produccion_aceptada'],
                                'producto' => $record->Fabricacion->producto,
                            ]);
                
                            Notification::make()
                                ->title('Producción registrada')
                                ->success()
                                ->body('Control de producción registrado correctamente.')
                                ->send();
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Error al guardar:', ['error' => $e->getMessage()]);
                            
                            Notification::make()
                                ->title('Error al registrar')
                                ->danger()
                                ->body('Hubo un error al registrar el control de producción: ' . $e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function getNavigationBadge(): ?string
{
    return ProduccionDiaria::where('registrado', false)->count();
}
}
