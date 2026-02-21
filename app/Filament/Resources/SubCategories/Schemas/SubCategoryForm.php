<?php

namespace App\Filament\Resources\SubCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->maxLength(255)
                                ->required(),
                            FileUpload::make('image')
                                ->maxSize(2028)
                                ->directory('Products\Categories')
                                ->image(),
                            Toggle::make('is_active')
                                ->required(),
                        ]),
                    TextInput::make('name')
                        ->maxLength(255)
                        ->required(),
                    Toggle::make('is_active')
                        ->required(),
                ])
                    ->columnSpan(3)
                    ->description('SubCategory Details'),
                Section::make([
                    FileUpload::make('image')
                        ->maxSize(2028)
                        ->directory('Products\SubCategories')
                        ->image(),
                ])
                    ->columnSpan(2)
                    ->description('SubCategory Image'),
            ])->columns(5);
    }
}
