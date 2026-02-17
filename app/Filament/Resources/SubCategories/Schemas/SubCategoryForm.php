<?php

namespace App\Filament\Resources\SubCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                FileUpload::make('image')
                    ->maxSize(2048)
                    ->directory('Products\SubCategories')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
