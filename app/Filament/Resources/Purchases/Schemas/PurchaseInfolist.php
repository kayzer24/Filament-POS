<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('purchase_number'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('supplier_id')
                    ->numeric(),
                TextEntry::make('purchase_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('received_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('tax_rate')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('discount')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('total_payment')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('status_payment'),
                TextEntry::make('payment_method'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
