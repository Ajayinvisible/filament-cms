<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->live(onBlur: true)
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(function (string $operation, $state, Set $set) {
                        if ($operation !== 'edit') {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->label('Slug'),
                ColorPicker::make('text_color')
                    ->default('#272727')
                    ->label('Text Color')
                    ->nullable(),
                ColorPicker::make('bg_color')
                    ->default('#e7e7e7')
                    ->label('Background Color')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('text_color')
                    ->label('Text Color')
                    ->html()
                    ->formatStateUsing(fn($state) => "<div style='display: flex; align-items: center; gap: 0.5rem;'>
        <span style='width: 1rem; height: 1rem; background-color: {$state}; border-radius: 2px; display: inline-block;'></span>
        <span>{$state}</span>
    </div>"),

                TextColumn::make('bg_color')
                    ->label('Background Color')
                    ->html()
                    ->formatStateUsing(fn($state) => "<div style='display: flex; align-items: center; gap: 0.5rem;'>
        <span style='width: 1rem; height: 1rem; background-color: {$state}; border-radius: 2px; display: inline-block;'></span>
        <span>{$state}</span>
    </div>"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
