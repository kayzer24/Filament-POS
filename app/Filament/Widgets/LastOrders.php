<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LastOrders extends TableWidget
{
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Order::query()->latest()->take(5))
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('customer.name')
                    ->sortable(),
                TextColumn::make('total_payment')
                    ->money('eur', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'completed' => 'success',
                        'processing' => 'warning',
                        'cancelled' => 'danger'
                    })
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
