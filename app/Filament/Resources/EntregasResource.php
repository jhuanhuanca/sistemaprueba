<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregasResource\Pages;
use App\Filament\Resources\EntregasResource\RelationManagers;
use App\Models\Entregas;
use App\Models\OrdenProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class EntregasResource extends Resource
{
    protected static ?string $model = Entregas::class;

    protected static ?string $navigationIcon = 'heroicon-o-Arrow-Path';
    protected static ?string $navigationGroup = 'LOGISTICA/VENTAS/ENTREGAS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Sección de Información General
            Forms\Components\Section::make([
                Forms\Components\TextInput::make('codigo')
                ->default('ENT-' . random_int(1000, 99999))
                ->label('Codigo')
                ->readOnly()
                ->required()
                ->maxLength(255),
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('usuario')
                ->label('Encargado')
                ->default(fn () => Auth::user()->name)
                ->readOnly()
                ->required(),
                Forms\Components\Select::make('orden_produccion_id')
                    ->relationship('ordenproduccion', 'codigo')
                    ->label('Orden de Producción')
                    ->placeholder('Selecciona una orden de producción')
                    ->default(fn () => request()->get('orden_produccion_id'))
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $orden = OrdenProduccion::find($state);
                        $producto = request()->get('producto') ?? $orden?->productos?->nombre ?? 'N/A';
                        $set('producto', $producto);
                        $cantidadProducir = request()->get('cantidad') ?? $orden?->cantidad_producir ?? 0;
                        $set('total', $cantidadProducir);
                    }),
                Forms\Components\TextInput::make('producto')
                    ->label('Producto')
                    ->readOnly()
                    ->default(fn () => request()->get('producto'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('color')
                    ->label('Color')
                    ->required()
                    ->maxLength(255),
            ])->columns(3),
            Forms\Components\Section::make([
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->readOnly()
                    ->default(fn () => request()->get('cantidad'))
                    ->required()
                    ->suffix('Unds')
                    ->numeric(),
            
            Forms\Components\TextInput::make('entregado')
                ->label('Entregado')
                ->required()
                ->numeric()
                ->suffix('Unds')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $total = $get('total') ?? 0;
                    $entregado = $state ?? 0;
                    
                    // Actualiza el faltante
                    $faltante = $total - $entregado;
                    $set('faltante', max(0, $faltante));
                    
                    // Actualiza el total para que sea igual a entregado + faltante
                    $set('total', $entregado + max(0, $faltante));
                }),
            
            Forms\Components\TextInput::make('faltante')
                ->label('Faltante')
                ->required()
                ->numeric()
                ->suffix('Unds')
                ->readOnly(), 
            ])->columns(3),
            
            // Sección de Observaciones
            Forms\Components\Textarea::make('observaciones')
                ->label('Observaciones')
                ->columnSpanFull(), // Ocupará todo el ancho del formulario
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordenproduccion.codigo')
                    ->label('Código de Producción')
                    ->sortable()
                    ->label('produccion'),
                Tables\Columns\TextColumn::make('producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entregado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('faltante')
                    ->numeric()
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
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->modalContent(function ($record) {
                        return view('pdf.recibo-entrega', [
                            'entrega' => $record
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
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
            'index' => Pages\ListEntregas::route('/'),
            'create' => Pages\CreateEntregas::route('/create'),
            'edit' => Pages\EditEntregas::route('/{record}/edit'),
        ];
    }
}
