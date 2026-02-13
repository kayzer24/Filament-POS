<?php

namespace App\Filament\Resources\Orders\RelationManagers;


use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'OrderDetails';

    protected static ?string $relatedResource = OrderResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->minLength(255),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product.image')->label('Image'),
                TextColumn::make('product.name')->label('Name'),
                TextColumn::make('product.price')->label('Price'),
                TextColumn::make('quantity'),
                TextColumn::make('subtotal'),
                // ...
            ])
            ->filters([
                //
            ])
            ->recordActions([
//                EditAction::make(),
//                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
