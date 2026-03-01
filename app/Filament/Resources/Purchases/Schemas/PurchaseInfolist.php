<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Models\Uom;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->label('Purchase Information')
                            ->schema([
                                TextEntry::make('purchase_number'),
                                TextEntry::make('user.name')
                                    ->label('Created By'),
                                TextEntry::make('purchase_date')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('received_date')
                                    ->date()
                                    ->placeholder('-'),
                            ])->columns(4),

                        Section::make()
                            ->schema([
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('status_payment')
                                    ->badge(),
                                TextEntry::make('payment_method')
                                    ->badge(),
                            ])->columns(3),
                    ])->columnSpan(2),


                Section::make()
                    ->label('Supplier Information')
                    ->schema([
                        TextEntry::make('supplier.name')
                            ->numeric(),
                        TextEntry::make('supplier.address'),
                        TextEntry::make('supplier.phone')
                            ->numeric(),
                    ])->columns(3),

                Group::make()
                    ->schema([
                        Section::make()
                            ->label('Purchase Details')
                            ->schema([
                                RepeatableEntry::make('purchaseDetails')
                                    ->hiddenLabel()
                                    ->schema([
                                        ImageEntry::make(
                                            'product.image'
                                        )->label('Image')
                                        ->imageSize(90, 90),
                                        TextEntry::make('product.name')
                                            ->label('Product Name'),
                                        TextEntry::make('price')
                                            ->money('eur'),
                                        TextEntry::make('Quantity')
                                            ->state(function ($record) {
                                                return ((int)$record->quantity) . ' ' . (Uom::find($record->purchase_unit)?->name);
                                            }),
                                        TextEntry::make('Total Quantity')
                                            ->state(function ($record) {
                                                return ((int)$record->total_quantity) . ' ' . (Uom::find($record->base_unit)?->name);
                                            }),
                                        TextEntry::make('subtotal')
                                            ->money('eur'),
                                    ])->columns(6),
                            ]),

                        Section::make()
                            ->label('Summary')
                            ->schema([
                                TextEntry::make('total_before_tax')
                                    ->label('Gross Total')
                                    ->numeric(),
                                TextEntry::make('tax_rate')
                                    ->label('Tax Rate (%)')
                                    ->numeric(),
                                TextEntry::make('tax_amount')
                                    ->money('eur', locale: 'fr_FR'),
                                TextEntry::make('discount')
                                    ->label('Discount Rate (%)')
                                    ->numeric(),
                                TextEntry::make('discount_amount')
                                    ->money('eur', locale: 'fr_FR'),
                                TextEntry::make('total_payment')
                                    ->numeric(),
                            ])->columns(6),
                    ])->columnSpanFull(),

            ])->columns(3);
    }
}
