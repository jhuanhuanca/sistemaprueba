<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreasResource\Pages;
use App\Filament\Resources\AreasResource\RelationManagers;
use App\Models\Areas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AreasResource extends Resource
{
    protected static ?string $model = Areas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Areas de trabajo';
    protected static ?string $modelLabel = 'Areas';
    protected static ?string $navigationGroup = 'GESTION DE RECURSOS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAreas::route('/'),
        ];
    }
}
