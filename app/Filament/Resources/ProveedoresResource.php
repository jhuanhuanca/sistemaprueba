<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedoresResource\Pages;
use App\Filament\Resources\ProveedoresResource\RelationManagers;
use App\Models\Proveedores;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class ProveedoresResource extends Resource
{
    protected static ?string $model = Proveedores::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Gestion de Proveedores';
    protected static ?string $modelLabel = 'Registro de Proveedores';
    protected static ?string $navigationGroup = 'RELACIONES COMERCIALES';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Información Principal')
                        ->schema([
                            Forms\Components\TextInput::make('nombre')
                                ->required()
                                ->maxLength(255)
                                ->label('Nombre del Proveedor'),
                            Forms\Components\TextInput::make('rut_cif_nit')
                                ->required()
                                ->maxLength(255)
                                ->label('RUT/CIF/NIT'),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('telefono')
                                ->tel()
                                ->required()
                                ->maxLength(255),
                        ]),
                    Forms\Components\Wizard\Step::make('Ubicación')
                        ->schema([
                            Forms\Components\TextInput::make('pais')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('ciudad')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('codigo_postal')
                                ->required()
                                ->maxLength(255),
                        ]),
                    Forms\Components\Wizard\Step::make('Detalles Adicionales')
                        ->schema([
                            Forms\Components\TextInput::make('sitio_web')
                                ->url()
                                ->maxLength(255),
                            Forms\Components\DatePicker::make('fecha_registro')
                                ->default(now())
                                ->required(),
                            Forms\Components\Toggle::make('estado')
                                ->onColor('success')
                                ->offColor('danger')
                                ->default(true)
                                ->required(),
                            Forms\Components\Textarea::make('notas')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rut_cif_nit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pais')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sitio_web')
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
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
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
            'index' => Pages\ManageProveedores::route('/'),
        ];
    }
}
