<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->components([
                       Section::make()
                            ->components([               
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required(),
                                FileUpload::make('avatar')
                                    ->disk('public')
                                    ->directory('avatars')
                                    ->image()
                                    ->preserveFilenames()
                            ]) 
                    ]),
                Group::make()
                    ->components([
                       Section::make()
                            ->components([
                                
                                Select::make('roles.name')
                                    ->label('Roles')
                                    ->relationship('roles', 'name')
                                    ->required(),
                                DateTimePicker::make('email_verified_at'),
                                TextInput::make('password')
                                    ->password()
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create'),
                            ]) 
                    ]),
            ]);
    }
}
