<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * Compétences réelles, confirmées par Eloge le 23/11/2026.
 * Le seeder remplace toutes les compétences existantes.
 * Un nom identique dans les deux langues peut rester une simple chaîne.
 */
class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            [
                'category' => ['fr' => 'Backend', 'en' => 'Backend'],
                'names' => ['PHP', 'Laravel', 'MySQL', 'API REST'],
            ],
            [
                'category' => ['fr' => 'Frontend', 'en' => 'Frontend'],
                'names' => ['React', 'JavaScript', 'Tailwind CSS', 'Inertia.js'],
            ],
            [
                'category' => ['fr' => 'IA', 'en' => 'AI'],
                'names' => [
                    ['fr' => "Création d'agents IA", 'en' => 'Building AI agents'],
                    ['fr' => "Entraînement d'agents IA", 'en' => 'Training AI agents'],
                    ['fr' => "Modèles d'IA variés", 'en' => 'Multiple AI models'],
                    'Claude Code',
                ],
            ],
            [
                'category' => ['fr' => 'Outils', 'en' => 'Tools'],
                'names' => ['Git', 'GitHub', 'GitLab', 'Docker', 'VPS', 'Figma'],
            ],
        ];

        Skill::query()->delete();

        foreach ($skills as $group) {
            foreach ($group['names'] as $order => $name) {
                Skill::create([
                    'name' => is_array($name) ? $name : ['fr' => $name, 'en' => $name],
                    'category' => $group['category'],
                    'order' => $order,
                ]);
            }
        }
    }
}
