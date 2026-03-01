<?php

namespace App\Filament\Resources\Purchases\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchase_number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('received_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_before_tax')
                    ->label('Gross payment')
                    ->money('eur', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('tax_rate')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->money('eur', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('discount')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->money('eur', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('total_payment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->money('eur', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status_payment')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
