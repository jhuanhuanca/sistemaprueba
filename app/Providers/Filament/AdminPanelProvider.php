<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Profile;
use App\Filament\Widgets\TestWidget;
use Filament\Events\Auth\Registered;
use Filament\Pages\Auth\Register;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use App\Filament\Pages\CrearMezcla;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('Dashboard')
            ->path('Dashboard')
            ->login(Login::class)
            ->brandName('Inicio')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Orange,
            ])
            ->font('Helvetica')
            ->favicon('images/logo.png')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                // Widgets\OrdersChart::class,  // Comenta o elimina si no existen
                // Widgets\RevenueChart::class, // Comenta o elimina si no existen
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                'panels::global-search.before',
                fn (): string => '
                    <div class="flex items-center space-x-8 mr-4">
                        <button 
                            x-data="{ isOpen: false }"
                            @click="isOpen = !isOpen"
                            @click.away="isOpen = false"
                            class="text-gray-400 hover:text-gray-500 flex flex-col items-center px-4 relative"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="text-xs mt-1">Notificaciones</span>
                            <div x-show="isOpen" 
                                 class="absolute top-full mt-2 w-80 bg-white rounded-lg shadow-lg p-4 z-50"
                                 style="display: none;"
                            >
                                <h3 class="text-lg font-medium mb-2">Notificaciones</h3>
                                <div class="border-t pt-2">
                                    <p>No tienes notificaciones</p>
                                </div>
                            </div>
                        </button>

                        <button 
                            x-data="{ isOpen: false }"
                            @click="isOpen = !isOpen"
                            @click.away="isOpen = false"
                            class="text-gray-400 hover:text-gray-500 flex flex-col items-center px-4 relative"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span class="text-xs mt-1">Mensajes</span>
                            <div x-show="isOpen" 
                                 class="absolute top-full mt-2 w-80 bg-white rounded-lg shadow-lg p-4 z-50"
                                 style="display: none;"
                            >
                                <h3 class="text-lg font-medium mb-2">Mensajes</h3>
                                <div class="border-t pt-2">
                                    <p>No tienes mensajes pendientes</p>
                                </div>
                            </div>
                        </button>

                        <button 
                            x-data="{ isOpen: false }"
                            @click="isOpen = !isOpen"
                            @click.away="isOpen = false"
                            class="text-gray-400 hover:text-gray-500 flex flex-col items-center px-4 relative"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs mt-1">Ayuda</span>
                            <div x-show="isOpen" 
                                 class="absolute top-full mt-2 w-80 bg-white rounded-lg shadow-lg p-4 z-50"
                                 style="display: none;"
                            >
                                <h3 class="text-lg font-medium mb-2">Ayuda</h3>
                                <div class="border-t pt-2">
                                    <p>ir a pagina de ayuda</p>
                                </div>
                            </div>
                        </button>
                    </div>
                '
            )
            ->profile(null)
            ->userMenuItems([
                MenuItem::make()
                    ->label('Mi Perfil')
                    ->url(fn (): string => Profile::getUrl())
                    ->icon('heroicon-o-user'),
                    
                MenuItem::make()
                    ->label('Configuración')
                    ->url('/admin/settings')
                    ->icon('heroicon-o-cog-6-tooth'),
                    
                MenuItem::make()
                    ->label('Mis Actividades')
                    ->url('/admin/activities')
                    ->icon('heroicon-o-clock'),
                    
                
            ])
            
            ->defaultAvatarProvider(
                \App\Providers\CustomAvatarProvider::class
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            //->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->spa();
            
    }
}
