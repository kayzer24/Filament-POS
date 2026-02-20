<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BestSelling extends TableWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Best Seller';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => OrderDetail::query()->select('product_id as id', 'product_id', DB::raw('SUM(quantity) as total_sold'))
                ->with('product')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->take(5)
            )
            ->columns([
                ImageColumn::make('product_image')
                ->label('Image')
                ->getStateUsing(fn($record)=>$record->product->image ?? 'N/A'),
                TextColumn::make('product_name')
                    ->label('Product Name')
                    ->formatStateUsing(fn (string $state) => Str::limit($state, 30))
                    ->getStateUsing(fn($record)=>$record->product->name ?? 'N/A'),
                TextColumn::make('total_sold')
                    ->label('Total Sold'),
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
