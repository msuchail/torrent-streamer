<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovieResource\Pages;
use App\Models\Movie;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MovieResource extends Resource
{
    protected static ?string $model = Movie::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $movie = $form->getRecord();


        return $form
            ->schema([
                Forms\Components\FileUpload::make('torrent')
                    ->disk('local')
                    ->directory('torrents')
                    ->visibleOn('create')
                    ->acceptedFileTypes(['application/x-bittorrent'])
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->disk("s3")
                    ->image()
                    ->directory("images")
                    ->previewable()
                    ->imageCropAspectRatio('16:9')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('description')
                    ->autosize()
                    ->rows(5)
                    ->maxLength(1000)
                    ->required(),
                Forms\Components\Select::make('groups')
                    ->relationship(titleAttribute: 'name')
                    ->preload()
                    ->multiple(),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
            ])
            ->poll('2s')
            ->filters([
                //
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMovies::route('/'),
            'create' => Pages\CreateMovie::route('/create'),
            'edit' => Pages\EditMovie::route('/{record}/edit'),
        ];
    }
}
