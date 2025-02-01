<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsumosResource\Pages;
use App\Filament\Resources\InsumosResource\RelationManagers;
use App\Models\Insumos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InsumosResource extends Resource
{
    protected static ?string $model = Insumos::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Insumos';
    protected static ?string $modelLabel = 'Insumo';
    protected static ?string $navigationGroup = 'GESTION DE INVENTARIOS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Información básica
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('descripcion')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make(name: 'image')
                            ->disk(name: 'public')
                            ->image()->preserveFilenames()
                            ->label('Imagen del Producto')
                            ->directory(directory: 'images'),
                    ])->columns(2),

                // Clasificación
                Forms\Components\Section::make('Clasificación')
                    ->schema([
                        Forms\Components\Select::make('area_id')
                            ->relationship(name: 'area', titleAttribute: 'descripcion')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Área'),
                        Forms\Components\Select::make('almacen_id')
                            ->relationship(name: 'almacen', titleAttribute: 'descripcion')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Almacen'),
                            Forms\Components\Select::make('tipo')
                            ->required()
                            ->options([
                                'reclicado' => 'Material Reciclado',
                                'virgen' => 'Material Virgen',
                            ])
                            ->label('Tipo'),
                        Forms\Components\Select::make('proveedor_id')
                            ->relationship(name: 'proveedor', titleAttribute: 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Proveedor'),
                        Forms\Components\TextInput::make('color')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                // Control de Inventario
                Forms\Components\Section::make('Control de Inventario')
                    ->schema([
                        Forms\Components\TextInput::make('fardos')
                            ->required()
                            ->reactive()
                            ->numeric()
                            ->suffix('Unds')
                            ->label('Cantidad de Fardos')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (!$state || !$get('pesoxfardo')) return;
                                
                                $stock = floatval($state) * floatval($get('pesoxfardo'));
                                if ($stock >= 1000) {
                                    $set('stock', number_format($stock / 1000, 3, '.', ''));
                                    $set('unidad', 'Toneladas');
                                } else {
                                    $set('stock', number_format($stock, 3, '.', ''));
                                    $set('unidad', 'Kilogramos');
                                }
                            }),
                        Forms\Components\TextInput::make('pesoxfardo')
                            ->required()
                            ->numeric()
                            ->reactive() 
                            ->suffix('kg')
                            ->label('Peso por Fardo')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (!$state || !$get('fardos')) return;
                                
                                $stock = floatval($get('fardos')) * floatval($state);
                                if ($stock >= 1000) {
                                    $set('stock', number_format($stock / 1000, 2, '.', ''));
                                    $set('unidad', 'Toneladas');
                                } else {
                                    $set('stock', number_format($stock, 2, '.', ''));
                                    $set('unidad', 'Kilogramos');
                                }
                            }),
                        Forms\Components\Fieldset::make('Stock Total')
                            ->schema([
                                Forms\Components\TextInput::make('stock')
                                    ->numeric()
                                    ->reactive()
                                    ->readOnly()
                                    ->required(),
                                Forms\Components\TextInput::make('unidad')
                                    ->reactive()
                                    ->required()
                                    ->default('Kilogramos')
                                    ->readOnly(),
                            ])->columns(2),
                        Forms\Components\TextInput::make('stock_min')
                            ->required()
                            ->numeric()
                            ->suffix('Unds.')
                            ->label('Stock Mínimo'),
                        Forms\Components\TextInput::make('stock_max')
                            ->required()
                            ->numeric()
                            ->suffix('Unds.')
                            ->label('Stock Máximo'),
                    ])->columns(2),

                // Costos
                Forms\Components\Section::make('Información de Costos')
                    ->schema([
                        Forms\Components\TextInput::make('costoLLegada')
                            ->numeric()
                            ->label('Costo de Llegada')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('costoTransporte')
                            ->numeric()
                            ->label('Costo de Transporte')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('costoEnvio')
                            ->numeric()
                            ->label('Costo de Envío')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('impuetoInportacion')
                            ->numeric()
                            ->label('Impuesto de Importación')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('costototal')
                            ->numeric()
                            ->default(0)
                            ->readonly()
                            ->label('Costo Total'),
                        Forms\Components\TextInput::make('costo_kilo')
                            ->numeric()
                            ->readonly()
                            ->live()
                            ->label('Costo por Kilo'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.descripcion')
                ->label('area laboral')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proveedor.nombre')->label('Proveedores')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->formatStateUsing(function ($state, $record) {
                        return number_format($state, 2, '.', '') . ' ' . $record->unidad;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\ImageColumn::make(name: 'image')
                    ->label(label: 'imagen'),
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
                Tables\Filters\SelectFilter::make('codigo')
                    ->label('Aumentar Stock')
                    ->searchable()
                    ->options(fn () => Insumos::pluck('codigo', 'codigo')->toArray())
                    ->placeholder('Buscar por código')
            ])
            ->filtersFormColumns(2)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Filtros')
            )
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
                Tables\Actions\Action::make('aumentarStock')
                    ->label('Aumentar Stock')
                    ->icon('heroicon-m-squares-plus')
                    ->form([
                        Forms\Components\TextInput::make('fardos_adicionales')
                            ->required()
                            ->numeric()
                            ->label('Fardos a Agregar')
                            ->default(0),
                        Forms\Components\TextInput::make('pesoxfardo')
                            ->required()
                            ->numeric()
                            ->label('Peso por Fardo')
                            ->default(fn ($record) => $record->pesoxfardo),
                    ])
                    ->action(function (Insumos $record, array $data): void {
                        $nuevosFardos = $record->fardos + $data['fardos_adicionales'];
                        $record->update([
                            'fardos' => $nuevosFardos,
                            'stock' => $nuevosFardos * $data['pesoxfardo']
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInsumos::route('/'),
        ];
    }

    private static function calculateCosts($state, callable $set, callable $get): void
    {
        $costoLLegada = floatval($get('costoLLegada') ?? 0);
        $costoTransporte = floatval($get('costoTransporte') ?? 0);
        $costoEnvio = floatval($get('costoEnvio') ?? 0);
        $impuestoImportacion = floatval($get('impuetoInportacion') ?? 0);
        $stock = floatval($get('stock') ?? 0);
        $unidad = $get('unidad');

        // Convertir a kilogramos si está en toneladas
        if ($unidad === 'Toneladas') {
            $stock = $stock * 1000;
        }

        // Calcular costo total
        $costoTotal = $costoLLegada + $costoTransporte + $costoEnvio + $impuestoImportacion;
        $set('costototal', $costoTotal);

        // Calcular costo por kilo
        if ($costoTotal > 0) {
            $costoKilo = $stock / $costoTotal;
            $set('costo_kilo', number_format($costoKilo, 2, '.', ''));
        }
    }
}
