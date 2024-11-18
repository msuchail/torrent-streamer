<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SerieResource\Pages;
use App\Filament\Resources\SerieResource\RelationManagers\SeasonsRelationManager;
use App\Models\Serie;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class SerieResource extends Resource
{
    use NestedResource;
    protected static ?string $model = Serie::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getAncestor(): ?\Guava\FilamentNestedResources\Ancestor
    {
        return null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('groups')
                    ->relationship(titleAttribute: 'name')
                    ->preload()
                    ->multiple(),
                Forms\Components\Select::make('status')
                    ->visibleOn('edit')
                    ->options([
                        'draft' => 'draft',
                        'published' => 'published',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->disk('s3')
                    ->image()
                    ->directory('images')
                    ->previewable()
                    ->imageCropAspectRatio('16:9')
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('environment')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            SeasonsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSerie::route('/create'),
            'edit' => Pages\EditSerie::route('/{record}/edit'),
            'seasons.create' => Pages\CreateSerieSeason::route('/{record}/seasons/create'),
        ];
    }
}
