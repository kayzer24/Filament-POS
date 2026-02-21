<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
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
                    ->description('Brand Details'),
                Section::make([
                    FileUpload::make('image')
                        ->maxSize(2028)
                        ->directory('Products\Brands')
                        ->image(),
                ])
                    ->columnSpan(2)
                    ->description('Brand Image'),
            ])->columns(5);
    }
}
