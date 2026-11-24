<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SkillController extends ResourceController
{
    protected string $route = 'skills';

    protected string $title = 'Compétences';

    protected string $singular = 'Compétence';

    /** Groupes affichés sur le site (FR => EN). */
    private const GROUPS = ['Backend' => 'Backend', 'Frontend' => 'Frontend', 'IA' => 'AI', 'Outils' => 'Tools'];

    protected function model(): string
    {
        return Skill::class;
    }

    protected function fields(): array
    {
        return [
            ['name' => 'group', 'type' => 'select', 'label' => 'Groupe', 'required' => true, 'virtual' => true,
                'options' => self::options(array_combine(array_keys(self::GROUPS), array_keys(self::GROUPS)))],
            ['name' => 'name', 'type' => 'translatable', 'label' => 'Nom', 'required' => true, 'help' => 'Nom court, sans phrase (ex. Docker).'],
            ['name' => 'order', 'type' => 'number', 'label' => 'Ordre dans le groupe', 'default' => 0],
        ];
    }

    protected function extraValues(Model $m): array
    {
        return ['group' => $m->exists ? $m->getTranslation('category', 'fr') : 'Backend'];
    }

    protected function columns(): array
    {
        return ['Groupe', 'Nom'];
    }

    protected function row(Model $m): array
    {
        return [$m->getTranslation('category', 'fr'), $m->getTranslation('name', 'fr')];
    }

    protected function query(): Builder
    {
        return Skill::query()->orderBy('category->fr')->orderBy('order');
    }

    protected function prepare(Model $model, array $data, Request $request): void
    {
        $model->setTranslations('category', ['fr' => $data['group'], 'en' => self::GROUPS[$data['group']]]);
    }
}
