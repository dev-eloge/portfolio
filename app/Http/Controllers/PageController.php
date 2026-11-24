<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Experience;
use App\Models\Skill;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /** Ordre d'affichage des groupes de compétences. */
    private const SKILL_CATEGORIES = ['Backend', 'Frontend', 'IA', 'Outils'];

    public function about(): Response
    {
        return Inertia::render('About', [
            'skillGroups' => $this->skillGroups(),
            'education' => $this->experiences(['education']),
            'experiences' => $this->experiences(['work', 'project']),
        ]);
    }

    public function skills(): Response
    {
        return Inertia::render('Skills', [
            'skillGroups' => $this->skillGroups(),
        ]);
    }

    public function experience(): Response
    {
        return Inertia::render('Experience', [
            'experiences' => $this->experiences(),
        ]);
    }

    public function certifications(): Response
    {
        $locale = app()->getLocale();

        return Inertia::render('Certifications', [
            'certifications' => Certification::ordered()->get()->map(fn (Certification $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'issuer' => $c->issuer,
                'issued' => ucfirst($c->issued_at->locale($locale)->translatedFormat('F Y')),
                'expires' => $c->expires_at ? ucfirst($c->expires_at->locale($locale)->translatedFormat('F Y')) : null,
                'expired' => $c->isExpired(),
                'credential_id' => $c->credential_id,
                'credential_url' => $c->credential_url,
                'skills' => $c->skills ?? [],
            ]),
        ]);
    }

    private function skillGroups(): Collection
    {
        // Regroupement sur le nom français (stable quelle que soit la langue affichée)
        return Skill::orderBy('order')->get()
            ->groupBy(fn (Skill $skill) => $skill->getTranslation('category', 'fr'))
            ->sortBy(fn ($skills, $category) => array_search($category, self::SKILL_CATEGORIES) === false
                ? PHP_INT_MAX
                : array_search($category, self::SKILL_CATEGORIES))
            ->map(fn ($skills, $category) => [
                'key' => $category,
                'category' => $skills->first()->category,
                'skills' => $skills->pluck('name')->values(),
            ])
            ->values();
    }

    private function experiences(?array $kinds = null): Collection
    {
        return Experience::query()
            ->when($kinds, fn ($q) => $q->whereIn('kind', $kinds))
            ->orderByRaw('ended_at IS NOT NULL')
            ->orderByDesc('started_at')
            ->orderBy('order')
            ->get()
            ->map(fn (Experience $e) => [
                'id' => $e->id,
                'kind' => $e->kind,
                'title' => $e->title,
                'company' => $e->company,
                'type' => $e->type,
                'description' => $e->description,
                'current' => $e->ended_at === null,
                'period' => self::period($e),
            ]);
    }

    /** Période à l'année : « 2024 », « 2022 – 2023 », « 2026 – Aujourd'hui ». */
    public static function period(Experience $e): string
    {
        $start = $e->started_at->year;

        if ($e->ended_at === null) {
            return $start.' – '.__("Aujourd'hui");
        }

        return $start === $e->ended_at->year ? (string) $start : $start.' – '.$e->ended_at->year;
    }
}
