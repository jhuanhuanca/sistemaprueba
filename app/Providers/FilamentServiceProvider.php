<?php

namespace App\Providers;

use App\Filament\Pages\FabricacionesPage;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Pages\Page;
use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\mesclados;
use Dompdf\Css\Color;
use App\Filament\Pages\Profile;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }
    public function boot(): void
    {
       
    
    }
    /**
     * Bootstrap services.
     */

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ... otras configuraciones ...
            ->pages([
                Profile::class,
            ]);
    }
}


