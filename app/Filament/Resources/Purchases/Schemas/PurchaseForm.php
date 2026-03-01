<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Models\BaseUnit;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Uom;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class PurchaseForm
{
    public static function generatePurchaseNumber()
    {
        return DB::transaction(function () {
            $year = date('Y');

            $last = Purchase::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('purchase_number')
                ->first();

            if ($last) {
                $lastNumber = (int)substr($last->purchase_number, -4);
                $next = $lastNumber + 1;
            } else {
                $next = 1;
            }

            $number = str_pad($next, 4, '0', STR_PAD_LEFT);

            return "PO-$year-$number";
        });
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FusedGroup::make([
                    TextInput::make('purchase_number')
                        ->default(fn() => self::generatePurchaseNumber())
                        ->hiddenLabel()
                        ->disabled()
                        ->readOnly()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->prefix('Purchase Number:')
                        ->required(),
                    Select::make('user_id')
                        ->options([auth()->id() => auth()->user()->name])
                        ->default(auth()->id())
                        ->hiddenLabel()
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->prefix('Created By:')
                        ->required(),
                ])->columnSpanFull(),

                Group::make([
                    Fieldset::make('Purchase Header')
                        ->schema([
                            Select::make('supplier_id')
                                ->required()
                                ->relationship('supplier', 'name')
                                ->label('Supplier Name'),
                            DatePicker::make('purchase_date')
                                ->default(today()->toDateString())
                                ->required()
                                ->label('Purchase Date'),
                            DatePicker::make('received_date')
                                ->required()
                                ->label('Received Date'),
                        ])->columns(3),

                    Fieldset::make('Purchase Details')
                        ->schema([
                            Repeater::make('purchase_details')
                                ->relationship('purchaseDetails')
                                ->hiddenLabel()
                                ->schema([
                                    Select::make('product_id')
                                        ->required()
                                        ->relationship('product', 'name')
                                        ->reactive()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $product = Product::with('baseUnit', 'purchaseUnit')->find($state);
                                            if (!$product) return;

                                            if (!$get('purchase_unit')) {
                                                $set('purchase_unit', $product->purchase_unit);
                                            }

                                            if (!$get('base_unit')) {
                                                $set('base_unit', $product->base_unit);
                                            }

                                            $set('conversion', $product->conversion_factor);

                                            if (!$get('quantity')) {
                                                $set('quantity', 1);
                                            }

                                            $set('total_quantity', $product->conversion_factor * $get('quantity'));

//                                            if ($product) {
//                                                $set('purchase_unit', $product->purchase_unit);
//                                                $set('base_unit', $product->base_unit);
//                                                $set('conversion', $product->conversion_factor);
//                                                $set('total_quantity', $product->conversion_factor * $get('quantity'));
//                                            }

                                        })->columnSpan(2),
                                    TextInput::make('price')
                                        ->default(0)
                                        ->numeric()
                                        ->prefix('€')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $qty = $get('quantity') ?? 1;
                                            $set('subtotal', $qty * $state);

                                            $items = $get('../../purchase_details') ?? [];
                                            $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                            $set('../../total_before_tax', $total);

                                            $tax = $get('../../tax_rate');

                                            if ($tax === null || $tax == 0) {
                                                $tax = 20;
                                                $set('../../tax_rate', $tax);
                                            }
                                            $taxAmount = ($tax / 100) * $total;
                                            $set('../../tax_amount', $taxAmount);

                                            $discount = $get('../../discount') ?? 0;
                                            $discountAmount = ($discount / 100) * $total;
                                            $set('../../discount_amount', $discountAmount);

                                            $set('../../total_payment', $total + $taxAmount - $discountAmount);

                                        }),
                                    FusedGroup::make()
                                        ->label('Quantity')
                                        ->schema([
                                            TextInput::make('quantity')
                                                ->reactive()
                                                ->numeric()
                                                ->required()
                                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                    $conversion = $get('conversion') ?? 1;
                                                    $price = $get('price') ?? 0;
                                                    $set('subtotal', $price * $state);
                                                    $set('total_quantity', $state * $conversion);

                                                    $items = $get('../../purchase_details') ?? [];
                                                    $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                                    $set('../../total_before_tax', $total);

                                                    $tax = $get('../../tax_rate') ?? 20;
                                                    $taxAmount = ($tax / 100) * $total;
                                                    $set('../../tax_amount', $taxAmount);

                                                    $discount = $get('../../discount') ?? 0;
                                                    $discountAmount = ($discount / 100) * $total;
                                                    $set('../../discount_amount', $discountAmount);

                                                    $set('../../total_payment', $total + $taxAmount - $discountAmount);
                                                })
                                                ->rule('numeric', 'min:1')
                                                ->minValue(0)
                                                ->default(0),
                                            Select::make('purchase_unit')
                                                ->options(Uom::pluck('name', 'id'))
                                                ->placeholder('Unit')
                                                ->disabled()
                                                ->dehydrated(),
                                        ])
                                        ->columns(2),


                                    Hidden::make('conversion'),

                                    FusedGroup::make()
                                        ->label('Total Quantity')
                                        ->schema([
                                            TextInput::make('total_quantity')
                                                ->readOnly()
                                                ->default(0),
                                            Select::make('base_unit')
                                                ->placeholder('Unit')
                                                ->options(BaseUnit::pluck('name', 'id'))
                                                ->disabled()
                                                ->dehydrated(),
                                        ])
                                        ->columns(2),
                                    TextInput::make('subtotal')
                                        ->readOnly()
                                        ->default(0)
                                        ->prefix('€'),
                                ])
                                ->hiddenLabel()
                                ->addAction(fn (Action $action) => $action
                                    ->label('Add Product')
                                    ->color('primary')
                                    ->icon(Heroicon::OutlinedPlus)
                                )
                                ->columns(3)
                                ->columnSpanFull(),
                        ]),
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
                        TextInput::make('total_before_tax')
                            ->reactive()
                            ->readOnly()
                            ->default(0)
                            ->required()
                            ->numeric()
                            ->columnSpanFull(),
                        FusedGroup::make([
                            TextInput::make('tax_rate')
                                ->readOnly()
                                ->default(0)
                                ->suffix('%')
                                ->required()
                                ->numeric(),
                            TextInput::make('tax_amount')
                                ->readOnly()
                                ->default(0)
                                ->prefix('€')
                                ->required()
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
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $total = $get('total_before_tax');
                                    $tax = $get('tax_rate') ?? 20;
                                    $taxAmount = ($tax / 100) * $total;
                                    $set('tax_amount', $taxAmount);

                                    $discount = $get('discount') ?? 0;
                                    $discountAmount = ($discount / 100) * $total;
                                    $set('discount_amount', $discountAmount);

                                    $set('total_payment', $total + $taxAmount - $discountAmount);
                                }),
                            TextInput::make('discount_amount')
                                ->readOnly()
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
                            ->readOnly()
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
