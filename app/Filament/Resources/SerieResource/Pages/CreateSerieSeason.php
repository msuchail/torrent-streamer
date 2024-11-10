<?php

namespace App\Filament\Resources\SerieResource\Pages;

use App\Filament\Resources\SerieResource;
use Filament\Actions;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateSerieSeason extends CreateRelatedRecord
{
    use NestedPage;
    protected static string $relationship = 'seasons';
    protected static string $resource = SerieResource::class;
}
