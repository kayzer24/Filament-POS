<?php

namespace App\Filament\Resources\BaseUnits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BaseUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required(),
                    Textarea::make('description')
                        ->required()
                        ->columnSpanFull(),
                ])
            ])->columns(1);
    }
}
