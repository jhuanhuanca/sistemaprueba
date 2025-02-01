<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenProduccionResource\Pages;
use App\Filament\Resources\OrdenProduccionResource\RelationManagers;
use App\Models\areas;
use App\Models\OrdenProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class OrdenProduccionResource extends Resource
{
    protected static ?string $model = OrdenProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Orden de Produccion';
    protected static ?string $modelLabel = 'Orden de Produccion';
    protected static ?string $navigationGroup = 'PLANIFICACION DE PRODUCCION';
    protected static ?int $navigationSort = 3;
   
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Wizard::make(steps: [
                Forms\Components\Wizard\Step::make(label: 'Detalles de Orden')
                ->schema(components: [
                    Forms\Components\TextInput::make(name: 'codigo')
                        ->label(label: 'codigo')
                        ->default(state: fn (): string=>'PRO-'. random_int (min: 1000,max: 999999))
                        ->readOnly()
                        ->required(),
                    Forms\Components\TextInput::make(name: 'usuario')
                        ->label(label: 'Encargado')
                        ->default(state: fn (): mixed => Auth::user()->name)
                        ->readOnly()
                        ->required(),
                    Forms\Components\Select::make(name: 'cliente_id')
                        ->relationship(name: 'clientes', titleAttribute: 'nombre')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('create')
                                ->label('Nuevo Cliente')
                                ->icon('heroicon-m-plus')
                                ->modalHeading('Crear Nuevo Cliente')
                                ->modalSubmitActionLabel('Crear y Seleccionar')
                                ->form([
                                    Forms\Components\TextInput::make('NitCi')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('nombre')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('ciudad')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('pais')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('telefono')
                                        ->tel()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\DatePicker::make('fecha_registro')
                                        ->default(now())
                                        ->readOnly()
                                        ->required(),
                                    Forms\Components\Toggle::make('estado')
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->accepted()
                                        ->declined(0)
                                            ->required(),
                                    Forms\Components\Textarea::make('notas')
                                        ->columnSpanFull(),
                                ])
                                ->action(function (array $data, Forms\Components\Select $component) {
                                    $cliente = \App\Models\Clientes::create([
                                        ...$data,
                                        'fecha_registro' => now(),
                                    ]);

                                    $component->state($cliente->id);

                                    Notification::make()
                                        ->title('Cliente creado exitosamente')
                                        ->success()
                                        ->send();
                                })
                        ),
                    Forms\Components\DateTimePicker::make(name: 'fecha_creacion')
                        ->default(state: now())
                        ->disabled()
                        ->required(),
                    Forms\Components\DateTimePicker::make(name: 'fecha_finalizacion_estimada'),
                    Forms\Components\DateTimePicker::make(name: 'fecha_entrega'),
                    Forms\Components\Select::make(name: 'estado')
                        ->options(options: [
                            'en espera'=>'En espera',
                            'en marcha'=>'En Marcha',
                            'revicion y cierre'=>'Revicion y Cierre',

                        ])
                        ->native(condition: false)
                            ->required(),  
                ])->columns(columns: 2),
                Forms\Components\Wizard\Step::make(label: 'Detalles de Produccion')
                ->schema(components: [
                    Forms\Components\Select::make(name: 'producto_id')
                        ->relationship(name: 'productos',titleAttribute: 'nombre')
                        ->required()
                        ->label('producto')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Textarea::make(name: 'descripcion')
                        ->columnSpanFull(),
                    Forms\Components\Select::make(name: 'area_id')
                        ->options(areas::query()->pluck('descripcion','id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make(name: 'cantidad_producir')
                        ->numeric()
                        ->required()
                        ->label('Cantidad')
                        ->suffix(label: 'unds.'),
                    Forms\Components\TextInput::make(name: 'color')
                        ->required(),
                    Forms\Components\Select::make(name: 'tipo_material')
                        ->options(options: [
                            'material virgen'=>'Plastico Virgen',
                            'material reciclado'=>'Plastico Reciclado',
                        ])
                        ->native(condition: false)
                        ->required(), 
                    Forms\Components\Textarea::make(name: 'observaciones')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make(name: 'costo_estimado')
                        ->required()
                        ->numeric()
                        ->suffix(label: '$Bs.'), 
                ])->columns(columns: 2),
            ])->columnSpanFull()
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            Tables\Columns\TextColumn::make('codigo')
                ->searchable(),
            Tables\Columns\TextColumn::make('areas.descripcion')->label ('area de trabajo')
                ->sortable(),
            Tables\Columns\TextColumn::make('productos.nombre')->label('productos')
                ->sortable(),
            Tables\Columns\TextColumn::make('cantidad_producir')
            ->label('Cantidad')
                ->formatStateUsing(fn(string $state): string => 'Uds.   '. $state)
                ->sortable(),
            Tables\Columns\TextColumn::make('color')
                ->searchable(),
            Tables\Columns\TextColumn::make('fecha_finalizacion_estimada')
                ->dateTime()
                ->label('Fecha Fin Estimada')
                ->searchable(),
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
                Tables\Actions\Action::make('view') // Botón de vista
                ->label('') // Solo ícono para ver
                ->icon('heroicon-o-eye') // Ícono de vista
                //->url(fn ($record) => route('', $record)) // URL dinámica
                ->color('primary'),
                Tables\Actions\EditAction::make()
                ->label('')
                ->color('warning')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenProduccions::route('/'),
            'create' => Pages\CreateOrdenProduccion::route('/create'),
            'edit' => Pages\EditOrdenProduccion::route('/{record}/edit'),
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
