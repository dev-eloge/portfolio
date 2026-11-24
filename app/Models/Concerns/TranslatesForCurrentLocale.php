<?php

namespace App\Models\Concerns;

use Spatie\Translatable\HasTranslations;

/**
 * Champs traduisibles (JSON {"fr": …, "en": …}).
 * À la sérialisation (props Inertia), seuls les textes de la langue active
 * sont envoyés, avec repli sur le français si la traduction manque.
 */
trait TranslatesForCurrentLocale
{
    use HasTranslations;

    public function toArray(): array
    {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = $this->getTranslation($field, app()->getLocale());
            }
        }

        return $attributes;
    }
}
