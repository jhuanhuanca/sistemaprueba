<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MezclasResource\Pages;
use App\Filament\Resources\MezclasResource\RelationManagers;
use App\Filament\Resources\MezclasResource\RelationManagers\MezclaMaterialRelationManager;
use App\Models\MezclaMaterial;
use App\Models\Mezclas;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MezclasResource extends Resource
{
    protected static ?string $model = Mezclas::class;

    protected static ?string $navigationIcon = 'heroicon-o-equals';
    protected static ?string $navigationLabel = 'Mezclas de Fabricacion';
    protected static ?string $modelLabel = 'Mezclas';
    protected static ?string $navigationGroup = 'REPROCESADOS/MESCLADOS';


    public static function calculateMasterCosts($state, callable $set, callable $get): void
    {
        $pesoMaster = floatval($get('peso_master') ?? 0);
        $master = \App\Models\Masters::find($get('master_id'));

        if ($master && $pesoMaster) {
            $costoM = $pesoMaster * $master->precioPorGramo;
            $costoMaster = $costoM / 100;
            $set('costo_master', $costoMaster);
        }
    }
    public static function calculateMezclaCosts($state, callable $set, callable $get): void
    {
        $costoMaster = floatval($get('costo_master') ?? 0);
        $costoMezcla = floatval($get('costo_mezcla') ?? 0);
        $costoTotal = floatval($get('costo_total') ?? 0);

            if ($costoMaster && $costoMezcla>0) {
            $costoTotal = $costoMaster + $costoMezcla;
            $set('costo_total', $costoTotal);
        }
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información General')
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->default('MEZ-'. random_int(100,999))
                        ->readOnly()
                        ->required(),
                    Forms\Components\DateTimePicker::make('fecha')
                        ->default(now())
                        ->disabled()
                        ->required(),
                    Forms\Components\TextInput::make('usuario')
                        ->label('Encargado')
                        ->default(fn(): mixed => Auth::user()->name)
                        ->readOnly()
                        ->required(),
                    Forms\Components\Toggle::make('estado')
                        ->onColor('success')
                        ->offColor('danger')
                        ->label('Estado de la Mezcla'),
                ])->columns(2),
            Section::make('Detalles de la Mezcla')
                ->schema([
                    Forms\Components\select::make('master_id')
                        ->relationship('masters', 'codigo')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            self::calculateMasterCosts($state, $set, $get);
                        }),
                    Forms\Components\TextInput::make('peso_master')
                        ->numeric()
                        ->default(0)
                        ->suffix('gr')
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            self::calculateMasterCosts($state, $set, $get);
                        }),
                    Forms\Components\TextInput::make('kilos_utilizados')
                        ->numeric()
                        ->live()
                        ->default(0)
                        ->suffix('Kg'),
                    Forms\Components\Select::make('tipo')
                        ->label('Tipo de Material')
                        ->options([
                            'material virgen' => 'Plastico Virgen',
                            'material reciclado' => 'Plastico Reciclado',
                            'virgen/reciclado' => 'Virgen/Reciclado'
                        ])
                        ->native(false)
                        ->required(),
                ])->columns(2),

            Section::make('Costos')
                ->schema([
                    Forms\Components\TextInput::make('costo_master')
                        ->numeric() 
                        ->suffix('Bs/gr')
                        ->readOnly()
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            self::calculateMezclaCosts($state, $set, $get);
                        }),
                    Forms\Components\TextInput::make('costo_mezcla')
                        ->numeric()
                        ->readOnly()
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            self::calculateMezclaCosts($state, $set, $get);
                        })
                        ->suffix('Bs/Kg'),
                    Forms\Components\TextInput::make('costo_total')
                        ->numeric()
                        ->live()
                        ->default(0)
                        ->readOnly()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            self::calculateMezclaCosts($state, $set, $get);
                        })
                        ->suffix('Bs'),
                ])->columns(3),

            Section::make('Cantidades')
                ->schema([
                    Forms\Components\TextInput::make('virgen')
                        ->label('Total M.Virgen')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->readOnly()
                        ->live()
                        ->suffix('%'),
                    Forms\Components\TextInput::make('reciclado')
                        ->label('Total M.Reciclado')
                        ->numeric()
                        ->default(0)
                        ->readOnly()
                        ->live()
                        ->suffix('%'),
                ])->columns(2),

            // Observaciones
            Forms\Components\Textarea::make('observaciones')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('codigo')
                ->sortable(),
            Tables\Columns\TextColumn::make('fecha')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('usuario')
                ->sortable(),
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
            Tables\Columns\TextColumn::make('cantidad_total')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('costo_estimado')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('costo_real')
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
            MezclaMaterialRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMezclas::route('/'),
            'create' => Pages\CreateMezclas::route('/create'),
            'edit' => Pages\EditMezclas::route('/{record}/edit'),
        ];
    }
}
