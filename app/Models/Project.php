<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\TranslatesForCurrentLocale;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use TranslatesForCurrentLocale;

    public array $translatable = ['title', 'category', 'description', 'context', 'problem', 'solution', 'result', 'image_alt'];

    protected $fillable = [
        'title', 'slug', 'category', 'category_slug', 'description',
        'context', 'problem', 'solution', 'result',
        'image', 'image_alt', 'technologies', 'url_live', 'url_code',
        'featured', 'order',
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('featured')->orderBy('order')->orderByDesc('id');
    }
}
