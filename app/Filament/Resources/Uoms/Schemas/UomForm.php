<?php

namespace App\Filament\Resources\Uoms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('code')
                        ->required(),
                    TextInput::make('name')
                        ->required(),
                    Select::make('base_unit_id')
                        ->relationship('baseUnit', 'name')
                        ->required(),
                    TextInput::make('symbol')
                        ->required(),
                    Textarea::make('description')
                        ->required()
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->required(),
                ]),
            ])->columns(1);
    }
}
