<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::where('status', 'new')->count())
                ->description('New orders waiting to be processed')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            Stat::make('Processing Orders', Order::where('status', 'processing')->count())
                ->description('Orders currently processing')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
            Stat::make('Orders Completed', Order::where('status', 'completed')->count())
                ->description('Order being completed successfully')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Total Revenue', Number::format(Order::where('status', 'completed')->sum('total_payment'), 2) . ' €')
                ->description('Total payment from completed orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
        ];
    }
}
