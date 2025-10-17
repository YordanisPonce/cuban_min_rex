<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Notifications\GeneratedPassword;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
        $pass = Str::random(8);

        $data['password'] = $pass;

        Notification::route('mail', $data['email'])->notify(new GeneratedPassword($data['name'],$data['email'], $pass));
        
        return $data;
    }
}
