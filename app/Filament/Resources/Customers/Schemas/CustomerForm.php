<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    TextInput::make('phone')
                        ->tel(),
                ])
                ->description('Personal details'),
                Section::make([
                    TextInput::make('address_line1 '),
                    TextInput::make('address_line2 '),
                    TextInput::make('postcode'),
                    TextInput::make('city'),
                    TextInput::make('state'),
                    TextInput::make('country'),
                ])
                    ->description('Billing details'),
            ])->columns(1);
    }
}
