<?php

namespace App\Providers;


use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\View;

class AppServiceProvider extends ServiceProvider
{
    public function login(): void
    {
        FilamentView::loginRenderHook(
            'panels::auth.login.form.after',
            fn(): View =>view('filament.recaptcha.error'),
        );
        FilamentView::loginRenderHook(
            'panels::auth.login.form.after',
            fn(): View =>view('filament.recaptcha.script'),
        );
        FilamentView::loginRenderHook(
            'panels::auth.login.form.after',
            fn(): View =>view('filament.recaptcha.info'),
        );
    }
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::RegisterRenderHook(
            'panels::auth.login.form.after',
            fn(): View =>view('filament.recaptcha.error'),
        );
        FilamentView::RegisterRenderHook(
            'panels::auth.login.form.after',
            fn(): View =>view('filament.recaptcha.script'),
        );
        FilamentView::RegisterRenderHook(
            'panels::auth.login.form.after',
            fn(): View =>view('filament.recaptcha.info'),
        );
    }
   

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

