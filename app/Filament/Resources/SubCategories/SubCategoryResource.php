<?php

namespace App\Filament\Resources\SubCategories;

use App\Filament\Resources\SubCategories\Pages\CreateSubCategory;
use App\Filament\Resources\SubCategories\Pages\EditSubCategory;
use App\Filament\Resources\SubCategories\Pages\ListSubCategories;
use App\Filament\Resources\SubCategories\Pages\ViewSubCategory;
use App\Filament\Resources\SubCategories\Schemas\SubCategoryForm;
use App\Filament\Resources\SubCategories\Schemas\SubCategoryInfolist;
use App\Filament\Resources\SubCategories\Tables\SubCategoriesTable;
use App\Models\SubCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare2Stack;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Square2Stack;

    protected static string | UnitEnum | null $navigationGroup = "Product Management";

    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'Category' => $record->category?->name ?? 'N/A',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return self::getUrl('view', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return SubCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubCategories::route('/'),
            'create' => CreateSubCategory::route('/create'),
            'view' => ViewSubCategory::route('/{record}'),
            'edit' => EditSubCategory::route('/{record}/edit'),
        ];
    }
}
