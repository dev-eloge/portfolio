<?php

namespace App\Models;

use App\Models\Concerns\TranslatesForCurrentLocale;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use TranslatesForCurrentLocale;

    public const ICONS = ['code', 'brain', 'layout', 'database', 'api', 'device-mobile'];

    public array $translatable = ['title', 'description'];

    protected $fillable = ['title', 'description', 'icon', 'order'];
}
