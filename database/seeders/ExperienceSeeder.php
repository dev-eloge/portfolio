<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

/**
 * Parcours réel d'Eloge (communiqué le 23/11/2026). Le seeder remplace toutes les expériences.
 * kind : education (formation), work (emploi, stage), project (hackathon, projet personnel).
 * Seules les années sont connues : l'affichage se fait à l'année.
 * Descriptions à compléter (null = non affichée).
 */
class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'kind' => 'work',
                'title' => ['fr' => 'Développeur fullstack', 'en' => 'Full-stack developer'],
                'company' => null,
                'type' => null,
                'started_at' => '2026-01-01',
                'ended_at' => null,
            ],
            [
                'kind' => 'project',
                'title' => ['fr' => 'Miabe-School', 'en' => 'Miabe-School'],
                'company' => null,
                'type' => ['fr' => 'Projet personnel', 'en' => 'Personal project'],
                'started_at' => '2025-01-01',
                'ended_at' => '2025-12-31',
            ],
            [
                'kind' => 'work',
                'title' => ['fr' => 'Stagiaire designer', 'en' => 'Design intern'],
                'company' => 'Orga Africa',
                'type' => ['fr' => 'Stage', 'en' => 'Internship'],
                'started_at' => '2024-01-01',
                'ended_at' => '2024-12-31',
            ],
            [
                'kind' => 'project',
                'title' => ['fr' => 'Clever-Green', 'en' => 'Clever-Green'],
                'company' => null,
                'type' => ['fr' => 'Hackathon', 'en' => 'Hackathon'],
                'started_at' => '2024-01-01',
                'ended_at' => '2024-12-31',
            ],
            [
                'kind' => 'education',
                'title' => ['fr' => 'Licence en génie logiciel', 'en' => "Bachelor's degree in software engineering"],
                'company' => null,
                'type' => ['fr' => 'Formation', 'en' => 'Education'],
                'started_at' => '2023-01-01',
                'ended_at' => null, // en fin de parcours, diplôme pas encore obtenu
            ],
            [
                'kind' => 'education',
                'title' => ['fr' => 'Baccalauréat scientifique', 'en' => 'Science baccalaureate'],
                'company' => null,
                'type' => ['fr' => 'Formation', 'en' => 'Education'],
                'started_at' => '2022-01-01',
                'ended_at' => '2023-12-31',
            ],
        ];

        Experience::query()->delete();

        foreach ($experiences as $order => $experience) {
            Experience::create([...$experience, 'description' => null, 'order' => $order + 1]);
        }
    }
}
