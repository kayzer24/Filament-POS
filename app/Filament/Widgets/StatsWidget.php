<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('The number of total products created')
                ->descriptionIcon(Heroicon::Squares2x2, IconPosition::Before)
                ->chart([1, 5, 12, 8, 20])
                ->color('info'),
            Stat::make('Total Customers', Customer::count())
                ->description('The number of total customers created')
                ->descriptionIcon(Heroicon::UserGroup, IconPosition::Before)
                ->chart([1, 5, 12, 8, 20])
                ->color('warning'),
            Stat::make('Total Orders', Order::count())
                ->description('The number of total orders created')
                ->descriptionIcon(Heroicon::ShoppingCart, IconPosition::Before)
                ->chart([1, 5, 12, 8, 20])
                ->color('primary'),
            Stat::make('Total Revenue', Number::format(Order::where('status', 'completed')->sum('total_payment'), 2) . ' €')
                ->description('Total payment from completed orders')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->chart([1, 5, 12, 8, 20])
                ->color('success'),

        ];
    }
}
