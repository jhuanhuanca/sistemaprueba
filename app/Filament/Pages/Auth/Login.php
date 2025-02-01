<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Pages\Auth\Login as AuthLogin;
use Filament\Pages\Page;

class Login extends AuthLogin
{
    public string $captchaToken;
    public function getRegisterFormAction(): Action{
        return Action::make('login')
        ->label(_('filament-panels::pages/auth/login.form.actions.login.label'))
        ->extraAttributes([
        'class'=>"g-recaptcha" ,
        'data-sitekey'=>"6Lcjei4qAAAAAHpve0tx4PDqkFaiQOn_8dZy_Bp9", 
        'data-callback'=>'handleCaptchar' ,
        'data-action'=>'login',
        ])
        ->submit('login');
    }

}
