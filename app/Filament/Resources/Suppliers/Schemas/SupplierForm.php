<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->autofocus()
                        ->required(),
                    TextInput::make('phone')
                        ->tel()
                        ->required(),
                    Toggle::make('is_active')
                        ->required(),
                ])
                    ->columns(2)
                    ->description('Personal Information'),
                Section::make([
                    TextInput::make('address')
                        ->required(),
                    TextInput::make('cp_name')
                        ->label('Sales Name')
                        ->required(),
                    TextInput::make('cp_phone')
                        ->label('Phone Number')
                        ->tel()
                        ->required(),
                    TextInput::make('cp_email')
                        ->label('Email Address')
                        ->email()
                        ->required(),
                ])
                    ->description('Contact Person')
                    ->columns(2),


            ])
            ->columns(1);
    }
}
