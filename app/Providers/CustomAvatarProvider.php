<?php

namespace App\Providers;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

class CustomAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        if ($record instanceof \App\Models\User) {
            $avatar = $record->avatar ?? 'avatars/default-1.png';
            return asset('storage/' . $avatar);
        }

        return '';
    }
} 