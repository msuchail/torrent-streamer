<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonResource\Pages;
use App\Filament\Resources\SeasonResource\RelationManagers\EpisodesRelationManager;
use App\Models\Season;
use App\Models\Serie;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class SeasonResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Season::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationParentItem = SerieResource::class;


    public static function getAncestor(): ?\Guava\FilamentNestedResources\Ancestor
    {
        return Ancestor::make(
            'seasons',
            'serie'
        );
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->disk("s3")
                    ->image()
                    ->directory("images")
                    ->previewable()
                    ->imageCropAspectRatio('16:9')
                    ->required(),
                Forms\Components\FileUpload::make('torrent')
                    ->disk('local')
                    ->directory('torrents')
                    ->visibleOn('create')
                    ->acceptedFileTypes(['application/x-bittorrent'])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ])->columns(1);
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
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('torrent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('torrent_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serie_id')
                    ->numeric()
                    ->sortable(),
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
            ])->reorderable('order');
    }

    public static function getRelations(): array
    {
        return [
            EpisodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit' => Pages\EditSeason::route('/{record}/edit'),
            'episodes.create' => Pages\CreateSeasonEpisode::route('/{record}/episodes/create'),
        ];
    }
}
