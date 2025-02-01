<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentasResource\Pages;
use App\Filament\Resources\VentasResource\RelationManagers\DetalleVentasRelationManager;
use App\Models\Ventas;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VentasResource extends Resource
{
    protected static ?string $model = Ventas::class; 

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'LOGISTICA/VENTAS/ENTREGAS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Información General')
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->default('VENT-' . random_int(1000, 9999))
                                    ->readOnly()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('fecha_venta')
                                    ->default(now())
                                    ->readOnly()
                                    ->required(),
                                Forms\Components\TextInput::make('usuario')
                                    ->label('Encargado')
                                    ->default(fn (): mixed => Auth::user()->name)
                                    ->readOnly()
                                    ->required(),
                            ])->columns(3),

                        Forms\Components\Section::make('Cliente y Almacén')
                            ->schema([
                                Forms\Components\Select::make('almacen_id')
                                    ->relationship('almacenes', 'descripcion')
                                    ->label('Almacén')
                                    ->required()
                                    ->default(1)
                                    ->searchable()
                                    ->preload(),
                                Select::make('cliente_id')
                                    ->relationship('clientes', 'nombre')
                                    ->label('Cliente')
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->preload()
                                    ->suffixAction(
                                        Action::make('nuevo cliente')
                                            ->label('añadir cliente')
                                            ->button()
                                            ->color('primary')
                                            ->modalHeading('añadir cliente')
                                            ->form([
                                                Forms\Components\TextInput::make('nombre')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('direccion')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('ciudad')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('estado_provincia')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('pais')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('telefono')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\DatePicker::make('fecha_registro')
                                                    ->default(now())
                                                    ->readOnly()
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\Toggle::make('estado')
                                                    ->onColor('success')
                                                    ->offColor('danger')
                                                    ->accepted()
                                                    ->declined(0)
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\Textarea::make('notas')
                                                    ->columnSpanFull(),
                                            ])
                                            
                                            ->action(function (array $data, callable $set) {
                                                $cliente = \App\Models\Clientes::create($data); 
                                                $set('cliente_id', $cliente->id);

                                                Notification::make()
                                                    ->title('Cliente creado con éxito')
                                                    ->success()
                                                    ->send();
                                            })
                                    ),
                            ])->columns(2),

                        Forms\Components\Section::make('Información de Pago')
                            ->schema([
                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->reactive()
                                    ->suffix('Bs'),
                                Forms\Components\Select::make('metodo_pago')
                                    ->options([
                                        'al contado' => 'Al contado',
                                        'coutas' => 'En Cuotas',
                                    ])
                                    ->label('Método de Pago')
                                    ->native(false)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state === 'coutas') {
                                            $set('estado', 'adeudo');
                                        } else {
                                            $set('estado', 'procesando ...');
                                        }
                                    }),
                                Forms\Components\TextInput::make('estado')
                                    ->default('procesando ...')
                                    ->label('Estado')
                                    ->readOnly()
                                    ->required(),
                            ])->columns(3),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('codigo'),
                Tables\Columns\TextColumn::make('clientes.nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_venta')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completada' => 'success',
                        'procesando ...' => 'info',
                        'adeudo' => 'danger',
                    }),
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
                Tables\Filters\SelectFilter::make('almacen_id')
                    ->label('Almacén')
                    ->relationship(
                        'almacenes', 
                        'descripcion',
                        fn ($query) => $query->select(['id', 'codigo', 'descripcion'])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->descripcion}")
                    ->searchable()
                    ->preload()
                    ->indicator('Almacén'),
                
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('clientes', 'nombre')
                    ->searchable()
                    ->preload()
                    ->indicator('Cliente'),

                Tables\Filters\SelectFilter::make('usuario')
                    ->label('Usuario')
                    ->options(function() {
                        return Ventas::distinct()->pluck('usuario', 'usuario')->toArray();
                    })
                    ->searchable()
                    ->indicator('Usuario'),

                Tables\Filters\Filter::make('fecha_venta')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->indicator(function (array $data): ?string {
                        if (! $data['desde'] && ! $data['hasta']) {
                            return null;
                        }
                        
                        if (! $data['hasta']) {
                            return 'Desde ' . $data['desde'];
                        }
                        
                        if (! $data['desde']) {
                            return 'Hasta ' . $data['hasta'];
                        }
                        
                        return 'Fecha: ' . $data['desde'] . ' - ' . $data['hasta'];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_venta', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_venta', '<=', $date),
                            );
                    })
            ])
            ->filtersFormColumns(4)
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl');
    }

    public static function getRelations(): array
    {
        return [
            DetalleVentasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVentas::route('/create'),
            'edit' => Pages\EditVentas::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultColumns(): array
    {
        return [
            'codigo',
            'usuario',
            'clientes.nombre',
            'almacenes.descripcion'
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'codigo',
            'usuario',
            'clientes.nombre',
            'almacenes.descripcion'
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->codigo;
    }
}
