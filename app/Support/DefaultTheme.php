<?php

namespace App\Support;

class DefaultTheme
{
    public static function mode(): string
    {
        $theme = strtolower(trim((string) config('app.default_theme', 'system')));

        return match (true) {
            str_contains($theme, 'dark') => 'mrcatz-dark',
            str_contains($theme, 'light') => 'mrcatz-light',
            default => 'system',
        };
    }
}
