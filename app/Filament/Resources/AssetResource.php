<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommodityResource\Pages;
use App\Filament\Resources\CommodityResource\RelationManagers;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Room;
use App\Models\User;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-s-archive-box';

    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Number')
                    ->required()
                    ->readOnly()
                    ->unique()
                    ->maxLength(18)
                    ->minLength(18)
                    ->default(fn() => Asset::number())
                    ->placeholder('Enter the number of the asset'),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the name of the asset'),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->required()
                    ->type('number')
                    ->placeholder('Enter the quantity of the asset'),
                Forms\Components\Select::make('brand_id')
                    ->label('Brand')
                    ->required()
                    ->searchable()
                    ->options(Brand::pluck('name', 'id')->toArray())
                    ->placeholder('Select the category of the asset'),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->required()
                    ->searchable()
                    ->options(Category::pluck('name', 'id')->toArray())
                    ->placeholder('Select the category of the asset'),
                Forms\Components\Select::make('room_id')
                    ->label('Room')
                    ->required()
                    ->searchable()
                    ->options(Room::pluck('name', 'id')->toArray())
                    ->placeholder('Select the room of the asset'),
                Forms\Components\Select::make('condition')
                    ->label('Condition')
                    ->required()
                    ->options([
                        'new' => 'New',
                        'used' => 'Used',
                        'damaged' => 'Damaged',
                    ])
                    ->placeholder('Select the condition of the asset'),
                Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required()
                    ->placeholder('Select the date of the asset'),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->required()
                    ->searchable()
                    ->default(fn() => auth()->id())
                    ->options(User::pluck('name', 'id')->toArray())
                    ->placeholder('Select the user of the asset'),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn($record) => match ($record->condition) {
                        'new' => 'success',
                        'used' => 'warning',
                        'damaged' => 'danger',
                    })
                    ->icon(fn($record) => match ($record->condition) {
                        'new' => 'heroicon-o-check-circle',
                        'used' => 'heroicon-m-at-symbol',
                        'damaged' => 'heroicon-m-sparkles',
                    })
                    ->label('Condition'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->multiple()
                    ->options(Category::pluck('name', 'id')->toArray())
                    ->label('Category')
                    ->attribute('category_id')
                    ->searchable(),
                SelectFilter::make('room_id')
                    ->multiple()
                    ->options(Room::pluck('name', 'id')->toArray())
                    ->label('Room')
                    ->attribute('room_id')
                    ->searchable(),
                SelectFilter::make('brand_id')
                    ->multiple()
                    ->options(Brand::pluck('name', 'id')->toArray())
                    ->label('Brand')
                    ->attribute('brand_id')
                    ->searchable(),
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'new' => 'New',
                        'used' => 'Used',
                        'damaged' => 'Damaged',
                    ])
                    ->attribute('condition')
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->icon('heroicon-o-plus')
            ]);
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
