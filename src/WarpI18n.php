<?php

namespace Vleap\Warps;

class WarpI18n
{
    /**
     * Resolve a localizable text value to a string for the given locale.
     *
     * Falls back to 'en', then to the first available translation.
     */
    public static function resolve(string|array|null $text, string $locale): ?string
    {
        if (is_string($text) && $text !== '') {
            return $text;
        }

        if (is_array($text)) {
            return $text[$locale] ?? $text['en'] ?? array_values($text)[0] ?? null;
        }

        return null;
    }

    /**
     * Resolve with a guaranteed string fallback.
     */
    public static function resolveOrEmpty(string|array|null $text, string $locale): string
    {
        return self::resolve($text, $locale) ?? '';
    }
}
