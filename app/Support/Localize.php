<?php

namespace App\Support;

class Localize
{
    public const LOCALES = ['fr', 'en'];

    /**
     * Remplace récursivement chaque tableau ['fr' => …, 'en' => …]
     * par la valeur de la langue active (repli sur le français).
     */
    public static function value(mixed $value, ?string $locale = null): mixed
    {
        $locale ??= app()->getLocale();

        if (! is_array($value)) {
            return $value;
        }

        if (self::isTranslation($value)) {
            return self::value($value[$locale] ?? $value['fr'], $locale);
        }

        return array_map(fn ($item) => self::value($item, $locale), $value);
    }

    private static function isTranslation(array $value): bool
    {
        $keys = array_keys($value);

        return $keys !== [] && array_diff($keys, self::LOCALES) === [] && array_key_exists('fr', $value);
    }
}
