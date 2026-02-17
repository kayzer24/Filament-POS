<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                FileUpload::make('image')
                    ->maxSize(2028)
                    ->directory('Products\Categories')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
