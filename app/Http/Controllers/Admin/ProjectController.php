<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends ResourceController
{
    protected string $route = 'projects';

    protected string $title = 'Projets';

    protected string $singular = 'Projet';

    protected function model(): string
    {
        return Project::class;
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'type' => 'translatable', 'label' => 'Titre', 'required' => true],
            ['name' => 'category', 'type' => 'translatable', 'label' => 'Catégorie', 'required' => true, 'help' => 'Ex. Application web. Sert aussi de filtre sur la page Projets.'],
            ['name' => 'description', 'type' => 'translatable_textarea', 'label' => 'Description courte', 'required' => true, 'help' => '2 phrases, affichées sur la carte.'],
            ['name' => 'context', 'type' => 'translatable_textarea', 'label' => 'Contexte'],
            ['name' => 'problem', 'type' => 'translatable_textarea', 'label' => 'Problème'],
            ['name' => 'solution', 'type' => 'translatable_textarea', 'label' => 'Solution'],
            ['name' => 'result', 'type' => 'translatable_textarea', 'label' => 'Résultat', 'help' => 'Uniquement des faits vérifiables (chiffres réels). Vide = section masquée.'],
            ['name' => 'technologies', 'type' => 'tags', 'label' => 'Technologies', 'help' => 'Séparées par des virgules : Laravel, React, Tailwind CSS'],
            ['name' => 'url_live', 'type' => 'url', 'label' => 'Lien du site en ligne'],
            ['name' => 'url_code', 'type' => 'url', 'label' => 'Lien du code source'],
            ['name' => 'image', 'type' => 'image', 'label' => 'Image (capture du projet)', 'help' => 'JPG, PNG ou WebP, 4 Mo max. Format paysage conseillé.'],
            ['name' => 'image_alt', 'type' => 'translatable', 'label' => "Description de l'image (accessibilité)"],
            ['name' => 'featured', 'type' => 'checkbox', 'label' => 'Mettre à la une'],
            ['name' => 'order', 'type' => 'number', 'label' => "Ordre d'affichage", 'default' => 0],
        ];
    }

    protected function columns(): array
    {
        return ['Titre', 'Catégorie', 'À la une'];
    }

    protected function row(Model $m): array
    {
        return [$m->getTranslation('title', 'fr'), $m->getTranslation('category', 'fr'), $m->featured ? 'Oui' : ''];
    }

    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        return Project::ordered();
    }

    protected function prepare(Model $model, array $data, Request $request): void
    {
        $model->category_slug = Str::slug($data['category']['fr']);

        if (! $model->exists) {
            $base = Str::slug($data['title']['fr']);
            $slug = $base;
            for ($i = 2; Project::where('slug', $slug)->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }
            $model->slug = $slug;
        }
    }
}
