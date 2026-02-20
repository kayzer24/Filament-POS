<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->maxLength(255)
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->maxLength(255)
                        ->required(),
                    DateTimePicker::make('email_verified_at')
                        ->default(now())
                        ->readOnly()
                        ->dehydrated(),
                    TextInput::make('password')
                        ->password()
                        ->maxLength(255)
                        ->revealable()
                        ->dehydrated(fn(?string $state): bool => filled($state))
                        ->required(fn(string $operation): bool => $operation === 'create'),
                    Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                ])
                    ->columns(2)
                    ->description('User Details'),
            ])->columns(1);
    }
}
