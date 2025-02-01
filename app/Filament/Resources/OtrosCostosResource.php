<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtrosCostosResource\Pages;
use App\Filament\Resources\OtrosCostosResource\RelationManagers;
use App\Models\OtrosCostos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OtrosCostosResource extends Resource
{
    protected static ?string $model = OtrosCostos::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Otros Costos';
    protected static ?string $modelLabel = 'OTROS COSTOS';
    protected static ?string $navigationGroup = 'GESTION DE COSTOS';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('descripcion')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('monto')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('monto')
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
            'index' => Pages\ListOtrosCostos::route('/'),
            'create' => Pages\CreateOtrosCostos::route('/create'),
            'edit' => Pages\EditOtrosCostos::route('/{record}/edit'),
        ];
    }
}
