<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post Details')
                    ->schema([
                        TextInput::make('title')
                            ->live(onBlur: true)
                            ->required()
                            ->maxLength(255)
                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                if ($operation === 'edit') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            })->columnSpanFull(),
                        Select::make('categories')
                            ->relationship('categories', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->multiple(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->label('Slug'),
                        RichEditor::make('body')
                            ->required()
                            ->fileAttachmentsDirectory('posts/images')
                            ->columnSpanFull()
                    ])->columns(2),
                Section::make('Meta Information')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail Image')
                            ->image()
                            ->directory('posts/thumbnails')
                            ->maxSize(2048),
                        DateTimePicker::make('published_at')->nullable()
                            ->label('Publish Date')
                            ->default(now()),
                        Toggle::make('featured')
                            ->label('Featured')
                            ->default(false)
                            ->inline(false),
                        Select::make('user_id')
                            ->relationship('author', 'name')
                            ->label('Author')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(auth()->id()),

                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime()
                    ->sortable(),
                ToggleColumn::make('featured')
                    ->label('Featured')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
