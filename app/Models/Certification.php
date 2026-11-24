<?php

namespace App\Models;

use App\Models\Concerns\TranslatesForCurrentLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use TranslatesForCurrentLocale;

    public array $translatable = ['title'];

    protected $fillable = ['title', 'issuer', 'issued_at', 'expires_at', 'credential_id', 'credential_url', 'skills', 'order'];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'skills' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderByDesc('issued_at');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
