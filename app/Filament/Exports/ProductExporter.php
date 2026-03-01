<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('description'),
            ExportColumn::make('price'),
            ExportColumn::make('stock'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('image'),
            ExportColumn::make('brand.name'),
            ExportColumn::make('category.name'),
            ExportColumn::make('subCategory.name'),
            ExportColumn::make('is_active'),
            ExportColumn::make('in_stock'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('barcode'),
            ExportColumn::make('base_price'),
            ExportColumn::make('uom.name'),
            ExportColumn::make('base_unit'),
            ExportColumn::make('purchase_unit'),
            ExportColumn::make('conversion_factor'),
            ExportColumn::make('gross_margin'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
