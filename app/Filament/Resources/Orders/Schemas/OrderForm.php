<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('date')
                    ->default(now())
                    ->hiddenLabel()
                    ->prefix('Date:')
                    ->dehydrated()
                    ->disabled()
                    ->columnSpanFull(),
                Group::make()
                    ->schema([
                        Section::make()
                            ->description('Customer Information')
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->label('name')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $customer = Customer::find($state);
                                        $set('phone', $customer->phone ?? null);
                                        $set('city', $customer->city ?? null);
                                        $set('country', $customer->country ?? null);
                                    })
                                    ->createOptionForm([
                                        Section::make([
                                            TextInput::make('name')
                                                ->columnSpanFull()
                                                ->required(),
                                            TextInput::make('email')
                                                ->label('Email address')
                                                ->email()
                                                ->required(),
                                            TextInput::make('phone')
                                                ->tel(),
                                        ])->columns(2)
                                            ->description('Personal details'),
                                        Section::make([
                                            TextInput::make('address_line1 '),
                                            TextInput::make('address_line2 '),
                                            TextInput::make('postcode'),
                                            TextInput::make('city'),
                                            TextInput::make('state'),
                                            TextInput::make('country'),
                                        ])
                                            ->columns(2)
                                            ->description('Billing details'),
                                    ]),
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('phone')
                                            ->state(fn(Get $get) => Customer::find($get('customer_id'))?->phone ?? '-'),
                                        TextEntry::make('city')
                                            ->state(fn(Get $get) => Customer::find($get('customer_id'))?->city ?? '-'),
                                        TextEntry::make('country')
                                            ->state(fn(Get $get) => Customer::find($get('customer_id'))?->country ?? '-'),
                                    ]),
                            ]),
                        Section::make()
                            ->description('Order Details')
                            ->schema([
                                Repeater::make('orderDetails')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'name', modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true))
                                            ->reactive()
                                            ->preload()
                                            ->searchable()
                                            ->columnSpanFull()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $product = Product::find($state);
                                                $price = $product->price ?? null;
                                                $set('price', $price);

                                                $quantity = $get('quantity') ?? 1;
                                                $set('quantity', $quantity);
                                                $subtotal = $price * $quantity;
                                                $set('subtotal', $subtotal);

                                                $items = $get ('../../orderDetails') ?? [];
                                                $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                                $set('../../total_price', $total);

                                                $discount = $get('../../discount');
                                                $discount_amount = $total * $discount / 100;
                                                $set('../../discount_amount', $discount_amount);
                                                $set('../../total_payment', $total - $discount_amount);
                                            }),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('€')
                                            ->formatStateUsing(fn($state, Get $get) => $state ?? Product::find($get('product_id'))?->price ?? 0),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(function (Get $get) {
                                                $productId = $get('product_id');
                                                $product = Product::find($productId);
                                                return $product?->stock ?? 0;
                                            })
                                            ->default(1)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $price = $get('price') ?? 0;
                                                $set('subtotal', $price * $state);

                                                $items = $get ('../../orderDetails') ?? [];
                                                $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                                $set('../../total_price', $total);

                                                $discount = $get('../../discount');
                                                $discount_amount = $total * $discount / 100;
                                                $set('../../discount_amount', $discount_amount);
                                                $set('../../total_payment', $total - $discount_amount);

                                            }),
                                        TextInput::make('subtotal')
                                            ->numeric()
                                            ->readOnly()
                                            ->default(0)
                                            ->prefix('€'),
                                    ])->columns(4)
                                    ->hiddenLabel()
                                    ->addAction(fn (Action $action) => $action
                                        ->label('add Product')
                                        ->color('primary')
                                        ->icon('heroicon-o-plus')
                                    ),
                            ]),
                    ])->columnSpan(2),

                Section::make()
                    ->description('Payment Information')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed'
                            ])
                            ->default('new')
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('total_price')
                            ->numeric()
                            ->readOnly()
                            ->prefix('€')
                            ->default(0)
                            ->columnSpanFull(),
                        TextInput::make('discount')
                            ->suffix('%')
                            ->default(0)
                            ->numeric()
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $discount = (float)($state ?? 0);
                                $total_price = $get('total_price') ?? 0;
                                $discount_amount = $total_price * $discount / 100;
                                $set('discount_amount', $discount_amount);
                                $set('total_payment', $total_price - $discount_amount);
                            })
                            ->columnSpan(2),
                        TextInput::make('discount_amount')
                            ->label('Amount')
                            ->prefix('€')
                            ->readOnly()
                            ->default(0)
                            ->columnSpan(2),
                        TextInput::make('total_payment')
                            ->prefix('€')
                            ->readOnly()
                            ->default(0)
                            ->columnSpanFull(),

                        Select::make('payment_method')
                            ->columnSpan(2)
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'wallet' => 'Wallet',
                            ])->default('cash'),
                        Select::make('payment_status')
                            ->columnSpan(2)
                            ->options([
                                'paid' => 'Paid',
                                'unpaid' => 'Unpaid',
                                'failed' => 'Failed'
                            ])->default('unpaid'),
                    ])->columnSpan(1)
                    ->columns(4),
            ])->columns(3);
    }
}
