<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FusedGroup::make([
                    TextInput::make('purchase_number')
                        ->hiddenLabel()
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->prefix('Purchase Number:')
                        ->required(),
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->hiddenLabel()
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->prefix('Created By:'),
                ])->columnSpanFull(),

                Group::make([
                    Fieldset::make('Purchase Header')
                    ->schema([
                        TextInput::make('supplier_id')
                            ->label('Supplier Name'),
                        DatePicker::make('purchase_date')
                            ->label('Purchase Date'),
                        DatePicker::make('received_date')
                            ->label('Received Date'),
                    ])->columns(3)
                ])->columnSpan(2),

                Fieldset::make('Payment Information')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'received' => 'Received',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('subtotal')
                            ->default(0)
                            ->required()
                            ->numeric()
                            ->columnSpanFull(),
                        FusedGroup::make([
                            TextInput::make('tax_rate')
                                ->required()
                                ->default(0)
                                ->suffix('%')
                                ->numeric(),
                            TextInput::make('tax_amount')
                                ->required()
                                ->default(0)
                                ->prefix('€')
                                ->numeric(),
                        ])
                            ->label('Tax')
                            ->columnSpanFull()
                            ->columns(2),

                        FusedGroup::make([
                            TextInput::make('discount')
                                ->required()
                                ->default(0)
                                ->suffix('%')
                                ->numeric(),
                            TextInput::make('discount_amount')
                                ->required()
                                ->default(0)
                                ->prefix('€')
                                ->numeric(),
                        ])
                            ->label('Discount')
                            ->columnSpanFull()
                            ->columns(2),


                        TextInput::make('total_payment')
                            ->default(0)
                            ->prefix('€')
                            ->required()
                            ->columnSpanFull()
                            ->numeric(),

                        Select::make('status_payment')
                            ->options([
                                'paid' => 'paid',
                                'unpaid' => 'Unpaid',
                            ])
                            ->default('unpaid')
                            ->required()
                            ->default('unpaid'),
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'wallet' => 'Wallet',
                            ])
                            ->default('cash')
                            ->required()
                            ->default('cash'),
                    ])->columnSpan(1),


            ])->columns(3);
    }
}
