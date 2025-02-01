<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MastersResource\Pages;
use App\Filament\Resources\MastersResource\RelationManagers;
use App\Models\Masters;
use Closure;
use Doctrine\DBAL\Query\From;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\form;

class MastersResource extends Resource
{
    // Configuración básica
    protected static ?string $model = Masters::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'GESTION DE INVENTARIOS';

    // Función auxiliar para cálculos
    private static function calculateCosts($state, callable $set, callable $get): void
    {
        $precioLLegada = floatval($get('precioLLegada') ?? 0);
        $costoTransporte = floatval($get('costoTransporte') ?? 0);
        $costoEnvio = floatval($get('costoEnvio') ?? 0);
        $peso = floatval($get('peso') ?? 0);

        // Cálculo del costo final
        $costoFinal = $precioLLegada + $costoTransporte + $costoEnvio;
        $set('costofinal', $costoFinal);

        // Cálculo del precio por gramo
        if ($peso > 0) {
            $precioPorGramo = ($costoFinal / ($peso * 1000)) * 100;
            $set('precioPorGramo', number_format($precioPorGramo, 2, '.', ''));
        }
    }

    // Formulario principal
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Grupo 1: Información básica
            Forms\Components\TextInput::make('codigo')
                ->default('MAST-')
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state) {
                    if (!str_starts_with($state, 'MAST-')) {
                        $set('codigo', 'MAST-' . preg_replace('/[^0-9]/', '', $state));
                    }
                })
                ->rules('regex:/^MAST-\d*$/')
                ->required(),
            Forms\Components\Textarea::make('descripcion')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('peso')
                ->numeric()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $get) {
                    self::calculateCosts($state, $set, $get);
                }),
            Forms\Components\TextInput::make('color'),
            Forms\Components\Select::make('proveedor_id')
                ->relationship('proveedores','nombre'),

            // Grupo 2: Costos
            Forms\Components\TextInput::make('precioLLegada')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $get) {
                    self::calculateCosts($state, $set, $get);
                }),
            Forms\Components\TextInput::make('costoTransporte')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $get) {
                    self::calculateCosts($state, $set, $get);
                }),
            Forms\Components\TextInput::make('costoEnvio')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $get) {
                    self::calculateCosts($state, $set, $get);
                }),

            // Grupo 3: Resultados calculados
            Forms\Components\TextInput::make('precioPorGramo')
                ->numeric()
                ->readOnly(),
            Forms\Components\TextInput::make('costofinal')
                ->numeric()
                ->readOnly(),
            Forms\Components\DatePicker::make('fecha')
                ->default(now()),
        ]);
    }

    // Tabla de visualización
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columnas de información básica
                Tables\Columns\TextColumn::make('codigo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('descripcion')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('peso')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('color')->searchable(),

                // Columnas de fechas
                Tables\Columns\TextColumn::make('fecha')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMasters::route('/'),
            'create' => Pages\CreateMasters::route('/create'),
            'edit' => Pages\EditMasters::route('/{record}/edit'),
        ];
    }
}
