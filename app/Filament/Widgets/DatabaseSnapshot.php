<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DatabaseSnapshot extends Widget
{
    protected static string $view = 'filament.widgets.database-snapshot';


    public function downloadDump()
    {
        Artisan::call('snapshot:create dump');
        return Storage::disk('snapshots')->download('dump.sql');
    }
}
