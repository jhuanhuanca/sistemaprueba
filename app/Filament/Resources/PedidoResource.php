<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\PdfService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $navigationGroup = 'LOGISTICA/VENTAS/ENTREGAS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pedido')
                    ->schema([
                        Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('almacen_id')
                                    ->relationship(
                                        'almacen',
                                        'descripcion',
                                        fn ($query) => $query->select(['id', 'codigo', 'descripcion'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->descripcion}")
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\DateTimePicker::make('fecha')
                                    ->required()
                                    ->default(now()),
                            ])->columns(3),

                        Group::make()
                            ->schema([
                                Forms\Components\Select::make('cliente')
                                    ->relationship('clienteRelacion', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $cliente = \App\Models\Clientes::find($state);
                                        if ($cliente) {
                                            $set('telefono', $cliente->telefono);
                                        }
                                    }),
                                Forms\Components\TextInput::make('telefono')
                                    ->tel()
                                    ->maxLength(20)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('total')
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0)
                                    ->prefix('$Bs'),
                            ])->columns(3),

                        Forms\Components\Repeater::make('detalles_pedido')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('producto_id')
                                    ->relationship('producto', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('subproducto_id', null)),
                                Forms\Components\Select::make('subproducto_id')
                                    ->label('Subproducto')
                                    ->options(function (callable $get) {
                                        $productoId = $get('producto_id');
                                        if (!$productoId) return [];
                                        return \App\Models\SubProducto::where('producto_id', $productoId)
                                            ->get()
                                            ->mapWithKeys(fn ($subproducto) => [
                                                $subproducto->id => "{$subproducto->nombre} - {$subproducto->color} - {$subproducto->tipo} (Stock: {$subproducto->cantidad})"
                                            ]);
                                    })
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('cantidad')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),
                                Forms\Components\TextInput::make('precio_unitario')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('total')
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('$'),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Producto')
                            ->reorderable(false)
                            ->cloneable(false)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('almacen.descripcion')
                    ->label('Almacén')
                    ->sortable(),
                Tables\Columns\TextColumn::make('clienteRelacion.nombre')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('almacen_id')
                    ->label('Buscar Almacén')
                    ->relationship(
                        'almacen', 
                        'descripcion',
                        fn ($query) => $query->select(['id', 'codigo', 'descripcion'])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->descripcion}")
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('cliente')
                    ->label('Buscar Cliente')
                    ->relationship('clienteRelacion', 'nombre')
                    ->searchable()
                    ->preload(),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()->label('')
                ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()->label('')
                ->icon('heroicon-o-trash'),
                Tables\Actions\Action::make('vender pedido  ')
                ->label('Entregar Pedido')
                ->icon('heroicon-o-shopping-cart'),
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['codigo', 'clienteRelacion.nombre', 'producto.nombre'];
    }

   
}
