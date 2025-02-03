<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductosResource\Pages;
use App\Filament\Resources\ProductosResource\RelationManagers;
use App\Filament\Resources\ProductosResource\RelationManagers\SubProductoRelationManager;
use App\Models\Productos;
use App\Models\SubProducto;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\PdfService;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductosResource extends Resource
{
    protected static ?string $model = Productos::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $navigationGroup = 'GESTION DE INVENTARIOS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Producto')
                    ->schema([
                        Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\select::make('categoria_id')
                                    ->relationship('categorias','descripcion')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\select::make('almacen_id')
                                    ->relationship(
                                        'almacen',
                                        'descripcion',
                                        fn ($query) => $query->select(['id', 'codigo', 'descripcion'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->descripcion}")
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ])->columns(3),

                        Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('descripcion')
                                    ->rows(3),
                            ])->columns(1),

                        Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stock Total')
                                    ->numeric()
                                    ->reactive()
                                    ->default(0)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('stock_min')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('stock_max')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('peso_unitario')
                                    ->label('Peso Unitario Promedio')
                                    ->numeric()
                                    ->suffix('kg')
                                    ->readOnly()
                                    ->formatStateUsing(function ($record) {
                                        if (!$record) return 0;
                                        
                                        return number_format(
                                            $record->subProducto()->avg('peso') ?? 0,
                                            2,
                                            '.',
                                            ''
                                        );
                                    }),
                                Forms\Components\TextInput::make('costo_mercado')
                                    ->required()
                                    ->numeric(),
                            ])->columns(4),

                        Group::make()
                            ->schema([
                                Forms\Components\Toggle::make('estado')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->accepted()
                                    ->declined(0)
                                    ->required(),
                            ])->columns(1),
                    ])->columnSpan(2),

                Group::make()
                    ->schema([
                        Section::make('Cargar imagen')
                            ->schema([
                                FileUpload::make('image')
                                    ->disk('public')
                                    ->image()
                                    ->preserveFilenames(false)
                                    ->directory('productos')
                                    ->label('Imagen del Producto')
                                    ->visibility('public')
                                    ->required()
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif'])
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string => 
                                            'producto-' . time() . '-' . str_replace(' ', '-', $file->getClientOriginalName())
                                    ),
                            ])
                    ])->columnSpan(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public')
                    ->width(100)
                    ->height(50)
                    ->defaultImageUrl(url('/storage/productos/producto-dreamcatcher.jpg')),
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categorias.descripcion')
                    ->label('Categoría')
                    ->sortable(),
                Tables\Columns\TextColumn::make('almacen.descripcion')
                    ->label('Almacén')
                    ->sortable(),
                Tables\Columns\TextColumn::make('areas.descripcion')
                    ->label('Área')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->suffix(' Unds.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('peso_unitario')
                    ->numeric()
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('costo_mercado')
                    ->numeric()
                    ->suffix(' Bs.')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('estado')
                    ->label('Activo')
                    ->onIcon('heroicon-s-check-circle')
                    ->offIcon('heroicon-s-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),
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
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categorias', 'descripcion')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('almacen_id')
                    ->label('Almacén')
                    ->relationship('almacen', 'descripcion')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('areas', 'descripcion')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(3)
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
            SubProductoRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProductos::route('/create'),
            'edit' => Pages\EditProductos::route('/{record}/edit'),
        ];
    }
}
