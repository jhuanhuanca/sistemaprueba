<?php

namespace App\Filament\Resources\FabricacionResource\RelationManagers;

use App\Models\asientos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcesosRelationManager extends RelationManager
{
    protected static string $relationship = 'Procesos';

   

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->label('Codigo')
                    ->required()
                    ->reactive()
                    ->default(fn () => $this->ownerRecord->codigo)
                    ->readOnly(),
                    Forms\Components\Select::make('asiento_id')
                    ->relationship('asientos','descripcion')
                    ->label('tipo de proceso')
                    ->required(),
                    Forms\Components\TextInput::make('descripcion')
                    ->maxLength(255),  
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo')
            ->columns([
                Tables\Columns\TextColumn::make('codigo'),
                Tables\Columns\TextColumn::make('asientos.descripcion')->label('proceso'),
                Tables\Columns\TextColumn::make('descripcion'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Proceso')
                ->icon('heroicon-o-plus')
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
}
