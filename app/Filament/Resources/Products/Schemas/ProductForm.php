<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\BaseUnit;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Uom;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;

class ProductForm
{
    public static function generateSku(Get $get, Set $set): void
    {
        $brand = Brand::find($get('brand_id'));
        $category = Category::find($get('category_id'));
        $subCategory = SubCategory::find($get('sub_category_id'));

        if (!$brand || !$category || !$subCategory) {
            return;
        }

        $catCode = strtoupper(substr($category->name, 0, 3));
        $subCatCode = strtoupper(substr($subCategory->name, 0, 3));
        $brandCode = strtoupper(substr($brand->name, 0, 3));

        $lastSku = Product::where('category_id', $category->id)
            ->where('sub_category_id', $subCategory->id)
            ->where('brand_id', $brand->id)
            ->orderBy('id', 'desc')
            ->value('sku');

        $nextNumber = 1;
        if ($lastSku) {
            $parts = explode('-', $lastSku);
            $lastNumber = (int)end($parts);
            $nextNumber = $lastNumber + 1;
        }

        $sku = sprintf('%s-%s-%s-%03d', $catCode, $subCatCode, $brandCode, $nextNumber);
        $set ('sku', $sku);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make([
                        TextInput::make('name')
                            ->columnSpanFull()
                            ->required(),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                        TextInput::make('base_price')
                            ->numeric()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('gross_margin', Number::format(($get('price')??0) - ($get('base_price')??0)), 0);
                            }),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('gross_margin', Number::format(($get('price')??0) - ($get('base_price')??0)), 0);
                            }),
                        TextInput::make('gross_margin')
                            ->readOnly()
                            ->numeric()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('gross_margin', Number::format(($get('price')??0) - ($get('base_price')??0)), 0);
                            })
                            ->afterStateHydrated(function (callable $get, callable $set) {
                                $set('gross_margin', Number::format(($get('price')??0) - ($get('base_price')??0)), 0);
                            }),
                        Select::make('uom_id')
                            ->relationship('uom', 'code')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $uom = Uom::with('baseUnit')->find($state);

                                if ($uom) {
                                    $set('base_unit', $uom->baseUnit?->id);
                                    $set('purchase_unit', $uom->id);
                                }
                            })
                            ->afterStateHydrated(function ($state, callable $set) {
                                $uom = Uom::with('baseUnit')->find($state);

                                if ($uom) {
                                    $set('base_unit', $uom->baseUnit?->id);
                                    $set('purchase_unit', $uom->id);
                                }
                            }),
                        Select::make('base_unit')
                            ->reactive()
                            ->disabled()
                            ->options(BaseUnit::pluck('name', 'id')),
                        Select::make('purchase_unit')
                            ->reactive()
                            ->disabled()
                            ->options(Uom::pluck('name', 'id')),
                        TextInput::make('conversion_factor')
                            ->label('Conversion'),
                        TextInput::make('stock')
                            ->required()
                            ->numeric(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->unique()
                            ->required()
                            ->reactive(),
                        TextInput::make('barcode')
                            ->unique()
                            ->required()
                            ->numeric(),
                        Group::make([
                            Toggle::make('is_active')
                                ->required(),
                            Toggle::make('in_stock')
                                ->required(),
                        ]),
                    ])->columns(3)
                        ->description('Product Details')
                ])->columnSpan(2),

                Section::make([
                    Select::make('brand_id')
                        ->relationship('brand', 'name', fn($query) => $query->where('is_active', true))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        })
                        ->createOptionForm([
                            TextInput::make('name')
                                ->maxLength(255)
                                ->required(),
                            FileUpload::make('image')
                                ->maxSize(2028)
                                ->directory('Products\Brands')
                                ->image(),
                            Toggle::make('is_active')
                                ->default(true)
                                ->required(),
                        ]),
                    Select::make('category_id')
                        ->relationship('category', 'name', fn($query) => $query->where('is_active', true))
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        })
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
                    Select::make('sub_category_id')
                        ->label('Sub-category')
                        ->options(function (Get $get) {
                            $categoryId = $get('category_id');

                            if (!$categoryId) return [];

                            return SubCategory::where('category_id', $categoryId)->pluck('name', 'id');
                        })
                        ->reactive()
                        ->disabled(fn(callable $get) => $get('category_id') === null)
                        ->dehydrated()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        })
                        ->createOptionForm(fn(Get $get) =>[
                            Select::make('category_id')
                                ->options(Category::pluck('name', 'id'))
                                ->default($get('category_id'))
                                ->dehydrated()
                                ->disabled(),
                            TextInput::make('name')
                                ->maxLength(255)
                                ->required(),
                            FileUpload::make('image')
                                ->maxSize(2048)
                                ->directory('Products\SubCategories')
                                ->image(),
                            Toggle::make('is_active')
                                ->required(),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return SubCategory::create($data)->getKey();
                        }),
                    FileUpload::make('image')
                        ->columnSpanFull()
                        ->image(),
                ])->columnSpan(1)
                    ->description('Association')
            ])->columns(3);
    }
}
