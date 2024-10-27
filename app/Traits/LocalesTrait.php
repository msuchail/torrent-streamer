<?php

namespace App\Traits;

trait LocalesTrait
{
    public function matchingLanguage(string $lang, int $i): string
    {
        return match ($lang) {
            'fre', 'fra', 'fr' => 'Français',
            'eng', 'en', 'us' => 'Anglais',
            'es', 'esp', 'spa' => 'Espagnol',
            'jap', 'jp', 'jpn' => 'Japonais',
            default => 'Langue '. $i,
        };
    }
}
