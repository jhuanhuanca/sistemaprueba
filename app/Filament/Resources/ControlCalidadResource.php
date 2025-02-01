<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlCalidadResource\Pages;
use App\Filament\Resources\ControlCalidadResource\RelationManagers;
use App\Models\ControlCalidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ControlCalidadResource extends Resource
{
    protected static ?string $model = ControlCalidad::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Control de Calidad';
    protected static ?string $modelLabel = 'CONTROL DE CALIDAD';
    protected static ?string $navigationGroup = 'CALIDAD Y CONTROL';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->default(now())
                    ->readOnly()
                    ->required(),
                Forms\Components\Select::make('producto_id')
                    ->relationship('productos','nombre')
                    ->required(),
                Forms\Components\TextInput::make('usuario')
                    ->default(Auth::user()->name)
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('punto_inspeccion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('resultado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('defectos_encontrados')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('mediciones'),
                Forms\Components\Textarea::make('accion_correctiva')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productos.nombre')->label('productos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('inspector_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('punto_inspeccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resultado')
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListControlCalidads::route('/'),
            'create' => Pages\CreateControlCalidad::route('/create'),
            'edit' => Pages\EditControlCalidad::route('/{record}/edit'),
        ];
    }
}
