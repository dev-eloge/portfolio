<?php

namespace App\Models;

use App\Models\Concerns\TranslatesForCurrentLocale;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use TranslatesForCurrentLocale;

    public array $translatable = ['title', 'type', 'description'];

    public const KINDS = ['education', 'work', 'project'];

    protected $fillable = ['kind', 'title', 'company', 'type', 'started_at', 'ended_at', 'description', 'order'];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }
}
