<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;

class Profile extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static ?string $title = 'Perfil de Usuario';
    protected static ?string $navigationGroup = 'ADMINISTRACION';
    protected static string $view = 'filament.pages.profile';
    
    protected static bool $shouldRegisterNavigation = true;

    public ?array $data = [];
    public $user;
    public $name;
    public $email;
    public $avatar;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public bool $showEditForm = false;

    public static function getLabel(): string
    {
        return 'Perfil de Usuario';
    }

    public static function getNavigationLabel(): string
    {
        return 'Mi Perfil';
    }

    protected function getFormModel(): User
    {
        return $this->user;
    }

    public function mount(): void
    {
        $this->user = Auth::user();
        
        if ($this->showEditForm) {
            $this->form->fill([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar ?? 'avatars/default-1.png',
                'password' => $this->user->password,
            ]);
        }
    }

    public function updatedShowEditForm(): void
    {
        if ($this->showEditForm) {
            $this->form->fill([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar ?? 'avatars/default-1.png',
                'password' => $this->user->password,

            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Perfil')
                    ->description('Actualiza tu información personal')
                    ->schema([
                        Select::make('avatar')
                            ->label('Avatar')
                            ->options([
                                'avatars/default-1.png' => 'Avatar 1',
                                'avatars/default-2.png' => 'Avatar 2',
                                'avatars/default-3.png' => 'Avatar 3',
                                'avatars/default-4.png' => 'Avatar 4',
                            ])
                            ->selectablePlaceholder(false)
                            ->required()
                            ->columnSpanFull()
                            ->prefixIcon('heroicon-o-user-circle')
                            ->optionsLimit(4)
                            ->native(false),
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),
                        TextInput::make('current_password')
                            ->label('Contraseña Actual')
                            ->password()
                            ->revealable()
                            ->required(fn ($get) => $get('new_password') || $get('new_password_confirmation')),
                        TextInput::make('new_password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->revealable()
                            ->rules(['min:8'])
                            ->same('new_password_confirmation'),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirmar Nueva Contraseña')
                            ->password()
                            ->revealable(),
                    ])->columns([
                        'default' => 2,
                        'sm' => 1,
                    ])
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        try {
            // Preparar los datos de actualización básicos
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'avatar' => $data['avatar'],
            ];

            // Verificar si se está intentando cambiar la contraseña
            if (!empty($data['new_password'])) {
                // Primero validar la contraseña actual
                if (!Hash::check($data['current_password'], $this->user->password)) {
                    Notification::make()
                        ->title('Error')
                        ->body('La contraseña actual es incorrecta')
                        ->danger()
                        ->send();
                    return;
                }

                // Validar que las contraseñas nuevas coincidan
                if ($data['new_password'] !== $data['new_password_confirmation']) {
                    Notification::make()
                        ->title('Error')
                        ->body('Las contraseñas nuevas no coinciden')
                        ->danger()
                        ->send();
                    return;
                }

                // Actualizar la contraseña usando Hash::make()
                $updateData['password'] = Hash::make($data['new_password']);
            }

            // Actualizar el usuario con los nuevos datos
            $this->user->fill($updateData)->save();

            Notification::make()
                ->title('¡Perfil Actualizado!')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('No se pudo actualizar el perfil: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
} 