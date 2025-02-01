<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlProduccionResource\Pages;
use App\Filament\Resources\ControlProduccionResource\RelationManagers;
use App\Models\ControlProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ControlProduccionResource extends Resource
{
    protected static ?string $model = ControlProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Control de Fabricacion';
    protected static ?string $modelLabel = 'CONTROL POST INVENTARIO';
    protected static ?string $navigationGroup = 'CALIDAD Y CONTROL';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha') 
                ->readOnly()
                    ->required(),
                Forms\Components\Select::make('produccion_diaria_id')
                    ->label('Número de Lote')
                    ->options(function () {
                        return \App\Models\ProduccionDiaria::pluck('numero_lote', 'id');
                    })
                    ->searchable()

                    ->required()
                    ->preload(),
                Forms\Components\TextInput::make('nombre_operario')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('usuario')
                    ->label('Inspector')
                    ->default(Auth::user()->name)
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('produccion_aceptada')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->suffix('Unds.'),
                Forms\Components\TextInput::make('produccion_rechazada')
                    ->required()
                    ->numeric()
                    ->suffix('Unds.'),
                Forms\Components\Select::make('resultado')
                    ->required()
                    ->options([
                        'malo'=>'Malo',
                        'regular'=>'Regular',
                        'bueno'=>'Bueno',
                    ])
                    ->native(false),
                Forms\Components\Textarea::make('defectos_encontrados')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('produccion_diaria_id')
                    ->label('Número de Lote')
                    ->getStateUsing(function ($record) {
                        return $record->ProduccionDiaria->numero_lote ?? '-';
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre_operario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('produccion_aceptada')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('produccion_rechazada')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListControlProduccions::route('/'),
            'edit' => Pages\EditControlProduccion::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected function getCreateButtonUrl(): string
    {
        return '/admin/calidad';
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
