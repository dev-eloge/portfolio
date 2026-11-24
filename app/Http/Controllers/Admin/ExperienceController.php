<?php

namespace App\Http\Controllers\Admin;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ExperienceController extends ResourceController
{
    protected string $route = 'experiences';

    protected string $title = 'Parcours';

    protected string $singular = 'Étape du parcours';

    protected function model(): string
    {
        return Experience::class;
    }

    protected function fields(): array
    {
        return [
            ['name' => 'kind', 'type' => 'select', 'label' => 'Nature', 'required' => true, 'options' => self::options([
                'work' => 'Emploi ou stage', 'project' => 'Projet (hackathon, projet personnel…)', 'education' => 'Formation (diplôme)',
            ]), 'default' => 'work', 'help' => 'Les formations apparaissent dans « Formation » sur la page À propos.'],
            ['name' => 'title', 'type' => 'translatable', 'label' => 'Intitulé', 'required' => true],
            ['name' => 'company', 'type' => 'text', 'label' => 'Entreprise ou établissement'],
            ['name' => 'type', 'type' => 'translatable', 'label' => 'Type', 'help' => 'Ex. Stage, Freelance, Hackathon, Projet personnel'],
            ['name' => 'started_year', 'type' => 'year', 'label' => 'Année de début', 'required' => true, 'virtual' => true],
            ['name' => 'ended_year', 'type' => 'year', 'label' => 'Année de fin', 'virtual' => true, 'help' => 'Laisser vide si en cours (affiché « Aujourd’hui »).'],
            ['name' => 'description', 'type' => 'translatable_textarea', 'label' => 'Description'],
            ['name' => 'order', 'type' => 'number', 'label' => "Ordre (à années égales)", 'default' => 0],
        ];
    }

    protected function extraValues(Model $m): array
    {
        return ['started_year' => $m->started_at?->year ?? '', 'ended_year' => $m->ended_at?->year ?? ''];
    }

    protected function columns(): array
    {
        return ['Période', 'Intitulé', 'Nature'];
    }

    protected function row(Model $m): array
    {
        $kinds = ['work' => 'Emploi / stage', 'project' => 'Projet', 'education' => 'Formation'];

        return [
            $m->started_at->year.' – '.($m->ended_at?->year ?? 'aujourd’hui'),
            $m->getTranslation('title', 'fr'),
            $kinds[$m->kind] ?? $m->kind,
        ];
    }

    protected function query(): Builder
    {
        return Experience::orderByRaw('ended_at IS NOT NULL')->orderByDesc('started_at')->orderBy('order');
    }

    protected function prepare(Model $model, array $data, Request $request): void
    {
        $model->started_at = "{$data['started_year']}-01-01";
        $model->ended_at = filled($data['ended_year'] ?? null) ? "{$data['ended_year']}-12-31" : null;
    }

    protected function rules(): array
    {
        return [...parent::rules(), 'ended_year' => ['nullable', 'integer', 'min:1950', 'max:2100', 'gte:started_year']];
    }
}
