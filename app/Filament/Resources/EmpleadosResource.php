<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleadosResource\Pages;
use App\Filament\Resources\EmpleadosResource\RelationManagers;
use App\Models\Empleados;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;
use App\Notifications\UserCredentialsNotification;

class EmpleadosResource extends Resource
{
    protected static ?string $model = Empleados::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Gestion de Personal';
    protected static ?string $modelLabel = 'Empleado';
    protected static ?string $navigationGroup = 'GESTION DE PERSONAL';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                ->default('USER-' . random_int(1000, 99999))
                ->readOnly()
                ->required(),
                Forms\Components\TextInput::make('ci')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombres')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('apellidos')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('cargo')
                    ->options(options: [
                        'operario'=>'Operario',
                        'supervisor'=>'Supervisor',
                        'jefe de produccion'=>'Jefe de Produccion',
                        'jefe de area'=>'Jefe de Area',
                        'jefe de planta'=>'Jefe de Planta',
                        'jefe de calidad'=>'Jefe de Calidad',
                        'jefe de logistica'=>'Jefe de Logistica',
                        'jefe de ventas'=>'Jefe de Ventas',
                        'jefe de administracion'=>'Jefe de Administracion',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('salario')
                    ->required()
                    ->numeric()
                    ->suffix('$Bs.'),
                Forms\Components\TextInput::make('salario_hora')
                    ->required()
                    ->numeric()
                    ->suffix('$Bs./hr'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ci')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('salario')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => 'Bs. '. $state),
                Tables\Columns\TextColumn::make('salario/hora')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => 'Bs. '. $state),
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
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash'),
                Tables\Actions\Action::make('crearUsuario')
                    ->label('')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading('Crear nuevo usuario')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->default(fn (Empleados $record): string => 
                                strtolower($record->nombres) . '_' . strtolower($record->cargo))
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(true)
                            ->default(fn (Empleados $record): string => 
                                strtolower($record->apellidos) . '_' . $record->ci)
                            ->disabled()
                            ->revealable(),
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->options(Role::pluck('name', 'name'))
                            ->preload(),
                    ])
                    ->action(function (array $data, Empleados $record): void {
                        $password = strtolower($record->apellidos) . '_' . $record->ci;
                        
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => bcrypt($password),
                        ]);
                        
                        if (isset($data['roles'])) {
                            $user->assignRole($data['roles']);
                        }

                        // Enviar notificación por correo
                        $user->notify(new UserCredentialsNotification($data['name'], $password));

                        Notification::make()
                            ->title('Usuario creado exitosamente')
                            ->success()
                            ->send();
                    })
                    ->tooltip('Crear usuario'),
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
            'index' => Pages\ManageEmpleados::route('/'),
        ];
    }
}
