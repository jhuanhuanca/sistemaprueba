<?php

namespace App\Filament\Resources\VentasResource\RelationManagers;

use App\Models\Productos;
use App\Models\SubProducto;
use Illuminate\Validation\ValidationException;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DetalleVentasRelationManager extends RelationManager
{
    protected static string $relationship = 'DetalleVentas';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('almacen_id')
                ->relationship('almacenes', 'descripcion')
                ->default(function (RelationManager $livewire): int {
                    return $livewire->getOwnerRecord()->almacen_id;
                })
                ->required(),
            Forms\Components\Select::make('producto_id')
                ->options(function (callable $get) {
                    $almacenId = $get('almacen_id');
                    
                    return Productos::query()
                        ->where('almacen_id', $almacenId)
                        ->pluck('nombre', 'id');
                })
                ->required()
                ->reactive()
                ->searchable()
                ->afterStateUpdated(function (callable $set) {
                    $set('subproducto_id', null);
                }),
            Forms\Components\Select::make('subproducto_id')
                ->label('Subproducto')
                ->options(function (callable $get) {
                    $productoId = $get('producto_id');
                    
                    if (!$productoId) {
                        return [];
                    }

                    return Productos::find($productoId)
                        ->subProducto()
                        ->get()
                        ->mapWithKeys(function ($subProducto) {
                            return [
                                $subProducto->id => "{$subProducto->color} - {$subProducto->tipo} (Stock: {$subProducto->cantidad})"
                            ];
                        });
                })
                ->required()
                ->reactive()
                ->searchable()
                ->preload()
                ->afterStateUpdated(function (callable $set, $state) {
                    $subProducto = SubProducto::find($state);
                    if ($subProducto) {
                        $producto = $subProducto->productos;
                        $set('codigo', $producto->codigo);
                        $set('precio_unitario', $producto->costo_mercado);
                        // Puedes agregar más campos si los necesitas
                        $set('color', $subProducto->color);
                        $set('tipo', $subProducto->tipo);
                    }
                }),
            Forms\Components\TextInput::make('codigo')
                ->readOnly(),
            Forms\Components\TextInput::make('precio_unitario')
                ->numeric()
                ->required()
                ->suffix('Bs'),
            Forms\Components\TextInput::make('color')
                ->readOnly()
                ->dehydrated(false),
            Forms\Components\TextInput::make('tipo')
                ->readOnly()
                ->dehydrated(false),
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->reactive()
                ->afterStateUpdated(function (callable $set, callable $get, $state) {
                    // Calcular subtotal
                    $cantidad = $state ?: 0;
                    $precioUnitario = $get('precio_unitario') ?: 0;
                    $set('subtotal', $cantidad * $precioUnitario);

                    // Actualizar stock del subproducto
                    $subproductoId = $get('subproducto_id');
                    if ($subproductoId) {
                        $subproducto = SubProducto::find($subproductoId);
                        if ($subproducto) {
                            if ($subproducto->cantidad < $state) {
                                Notification::make()
                                    ->title('Error')
                                    ->danger()
                                    ->body("No hay suficiente stock disponible. Stock actual: {$subproducto->cantidad}")
                                    ->send();
                                $set('cantidad', null);
                                return;
                            }

                            // Actualizar cantidad del subproducto
                            $nuevaCantidad = $subproducto->cantidad - $state;
                            $subproducto->update(['cantidad' => $nuevaCantidad]);
                            
                            // Actualizar stock del producto padre
                            $producto = $subproducto->productos;
                            $producto->stock = $producto->subProducto()->sum('cantidad');
                            $producto->save();

                            Notification::make()
                                ->title('Stock actualizado correctamente')
                                ->success()
                                ->send();
                        }
                    }
                }),
            Forms\Components\TextInput::make('subtotal')
                ->readOnly()
                ->reactive()
                ->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo')
            ->columns([
                Tables\Columns\TextColumn::make('codigo'),
                Tables\Columns\TextColumn::make('productos.nombre'),
                Tables\Columns\TextColumn::make('cantidad'),
                Tables\Columns\TextColumn::make('precio_unitario')
                ->numeric()
                ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                ->numeric()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('añadir producto'),
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            // Aseguramos que el almacen_id se incluya en los datos
            $data['almacen_id'] = $this->getOwnerRecord()->almacen_id;
            
            // Reduce el stock del subproducto cuando se crea una nueva venta
            $this->updateInventory($data['subproducto_id'], $data['cantidad']);
            
            Notification::make()
                ->title('Stock actualizado correctamente')
                ->success()
                ->send();
            return $data;
        } catch (ValidationException $e) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body($e->getMessage())
                ->send();
            throw $e;
        }
    }

    private function updateInventory($subproductoId, $cantidad): void
    {
        $subproducto = SubProducto::find($subproductoId);

        if (!$subproducto) {
            throw ValidationException::withMessages([
                'subproducto_id' => "Subproducto no encontrado.",
            ]);
        }

        // Verifica si hay suficiente stock antes de la venta
        if ($subproducto->cantidad < $cantidad) {
            throw ValidationException::withMessages([
                'cantidad' => "No hay suficiente stock disponible. Stock actual: {$subproducto->cantidad}",
            ]);
        }

        // Reduce la cantidad del subproducto
        $subproducto->cantidad -= $cantidad;
        $subproducto->save();

        // Recalcula el stock total del producto padre
        $producto = $subproducto->productos;
        $producto->stock = $producto->subProducto()->sum('cantidad');
        $producto->save();
    }
}