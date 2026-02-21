<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->maxLength(255)
                        ->required(),
                    Toggle::make('is_active')
                        ->required(),
                ])
                    ->columnSpan(3)
                    ->description('Category Details'),
                Section::make([
                    FileUpload::make('image')
                        ->maxSize(2028)
                        ->directory('Products\Categories')
                        ->image(),
                ])
                    ->columnSpan(2)
                    ->description('Category Image'),
            ])->columns(5);
    }
}
