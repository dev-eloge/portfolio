<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Profil du portfolio : valeurs par défaut de config/portfolio.php,
 * remplacées par celles enregistrées depuis l'admin (table settings).
 * Les textes bilingues restent au format ['fr' => …, 'en' => …] : utiliser Localize::value().
 */
class Profile
{
    /** Clés modifiables depuis l'admin. */
    public const KEYS = ['name', 'role', 'intro', 'about', 'location', 'work_mode', 'email', 'github', 'linkedin', 'cv', 'images'];

    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $profile = collect(config('portfolio'))->only(self::KEYS)->all();

        foreach (Setting::whereIn('key', self::KEYS)->get() as $setting) {
            $profile[$setting->key] = $setting->key === 'images'
                ? array_replace($profile['images'] ?? [], $setting->value ?? [])
                : $setting->value;
        }

        return self::$cache = $profile;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    public static function save(array $values): void
    {
        foreach ($values as $key => $value) {
            if (in_array($key, self::KEYS, true)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }
        self::$cache = null;
    }

    public static function flush(): void
    {
        self::$cache = null;
    }
}
