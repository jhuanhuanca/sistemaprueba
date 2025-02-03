<?php

namespace App\Filament\Resources\ProductosResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubProductoRelationManager extends RelationManager
{
    protected static string $relationship = 'SubProducto';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información básica')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->reactive()
                            ->maxLength(255)
                            ->default(fn () => $this->ownerRecord->nombre)
                            ->readOnly(),
                        Forms\Components\TextInput::make('color')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tipo')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Inventario')
                    ->schema([
                        Forms\Components\TextInput::make('cantidad')
                            ->required()
                            ->reactive()
                            ->numeric()
                            ->label('Cantidad total')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $this->recalcularStock();
                                
                                $unidxpaq = $get('unidxpaq');
                                if ($unidxpaq != 0 && isset($state) && isset($unidxpaq)) {
                                    $set('paq', $state / $unidxpaq);
                                } else {
                                    $set('paq', null);  
                                }
                            }),
                        Forms\Components\TextInput::make('unidxpaq')
                            ->required()
                            ->reactive()
                            ->numeric()
                            ->label('Unidades por paquete')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $cantidad = $get('cantidad');
                                if ($state != 0 && isset($cantidad) && isset($state)) {
                                    $set('paq', $cantidad / $state);
                                } else {
                                    $set('paq', 0); 
                                }
                            }),
                        Forms\Components\TextInput::make('paq')
                            ->reactive()
                            ->label('Total de paquetes')
                            ->readOnly(),
                        Forms\Components\TextInput::make('peso')
                            ->required()
                            ->numeric()
                            ->label('Peso (kg)'),
                        Forms\Components\Toggle::make('disponible')
                            ->label('Disponible para venta')
                            ->default(true),
                    ])->columns(3),
            ]);
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad total')
                    ->searchable(), 
                Tables\Columns\TextColumn::make('unidxpaq')
                    ->label('Unid. x paquete')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paq')
                    ->label('Total paquetes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peso')
                    ->label('Peso (kg)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('disponible')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        $this->recalcularStock();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->after(function () {
                        $this->recalcularStock();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->after(function () {
                        $this->recalcularStock();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->recalcularStock();
                        }),
                ]),
            ]);
    }
    protected function recalcularStock(): void
    {
        $productoPadre = $this->ownerRecord;
        
        $productoPadre->load('subProducto');
        
        $totalCantidad = $productoPadre->subProducto()->sum('cantidad');
        
        $productoPadre->update(['stock' => $totalCantidad ?: 0]);
        
        $productoPadre->refresh();
        $this->refreshParentRecord();
    }

    /**
     * Refresca el registro del producto padre.
     */
    protected function refreshParentRecord(): void
    {
        $this->ownerRecord->refresh(); // Recarga los datos del padre
    }
}
