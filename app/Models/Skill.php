<?php

namespace App\Models;

use App\Models\Concerns\TranslatesForCurrentLocale;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use TranslatesForCurrentLocale;

    public array $translatable = ['name', 'category'];

    protected $fillable = ['name', 'category', 'order'];
}
