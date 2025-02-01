<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquiposResource\Pages;
use App\Filament\Resources\EquiposResource\RelationManagers;
use App\Models\Equipos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquiposResource extends Resource
{
    protected static ?string $model = Equipos::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Maquinarias-Equipos';
    protected static ?string $modelLabel = 'Maquinaria y Equipos';
    protected static ?string $navigationGroup = 'GESTION DE INVENTARIOS';

    private static function calculateCosts($state, callable $set, callable $get): void
    {
        $voltaje = floatval($get('voltaje') ?? 0);
        $amperaje = floatval($get('amperaje') ?? 0);
        $factorPotencia = floatval($get('factorPotencia') ?? 0);
        $depreciacionAnual = floatval($get('depreciacionAnual') ?? 0);
        $costoMantenimiento = floatval($get('costoMantenimiento') ?? 0);

        // Calcular consumo energético
        if ($voltaje > 0 && $amperaje > 0 && $factorPotencia > 0) {
            $consumo = sqrt(3) * $voltaje * $amperaje * $factorPotencia;
            $consumoEnergetico = $consumo /1000;
            $set('consumoEnergetico', number_format($consumoEnergetico, 2, '.', ''));
        }

        // Calcular costo de maquinaria
        if ($depreciacionAnual > 0 || $costoMantenimiento > 0) {
            $costoMaq = $depreciacionAnual + $costoMantenimiento;
            $set('costoMaq', number_format($costoMaq, 2, '.', ''));
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('descripcion')
                            ->columnSpan(2),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles del Equipo')
                    ->schema([
                        Forms\Components\Select::make('area_id')
                            ->required()
                            ->relationship('areas','descripcion')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('marca')
                            ->required(),
                        Forms\Components\TextInput::make('modelo')
                            ->required(),
                        Forms\Components\Select::make('proveedor_id')
                            ->relationship('proveedor','nombre')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('estado')
                            ->onColor('success')
                            ->offColor('danger')
                            ->accepted()
                            ->declined(0)
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->disk('public')
                            ->image()
                            ->preserveFilenames()
                            ->label('Imagen')
                            ->directory('images'),
                    ])->columns(3),

                Forms\Components\Section::make('Especificaciones Eléctricas')
                    ->schema([
                        Forms\Components\TextInput::make('voltaje')
                            ->numeric()
                            ->label('Voltaje (V)')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('amperaje')
                            ->numeric()
                            ->label('Amperaje (A)')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('factorPotencia')
                            ->numeric()
                            ->label('Factor de Potencia')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('consumoEnergetico')
                            ->numeric()
                            ->label('Consumo Energético (KW)')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                Forms\Components\Section::make('Información Financiera')
                    ->schema([
                        Forms\Components\TextInput::make('depreciacionAnual')
                            ->numeric()
                            ->label('Depreciación Anual ($)')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('costoMantenimiento')
                            ->numeric()
                            ->label('Costo de Mantenimiento ($)')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateCosts($state, $set, $get)),
                        Forms\Components\TextInput::make('costoMaq')
                            ->numeric()
                            ->label('Costo Total de Maquinaria ($)')
                            ->disabled()
                            ->dehydrated(),
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
                Tables\Columns\TextColumn::make('areas.descripcion')
                    ->label('area de trabajo')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('estado')
                    ->label('Activo')
                    ->onIcon('heroicon-s-check-circle') 
                    ->offIcon('heroicon-s-x-circle')
                    ->onColor('success') 
                    ->offColor('danger') 
                    ->action(function ($record) {
                        $record->status = !$record->status;
                        $record->save();
                    }),
                Tables\Columns\ImageColumn::make(name: 'image')
                    ->label(label: 'imagen'),
                Tables\Columns\TextColumn::make('consumoEnergetico')
                    ->label('Consumo Energético')
                    ->suffix('   KW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('costoMaq')
                    ->label('Costo Maquinaria')
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
                Tables\Actions\EditAction::make()
                ->label('')
                ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->icon('heroicon-o-trash'),
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
            'index' => Pages\ManageEquipos::route('/'),
        ];
    }
}
