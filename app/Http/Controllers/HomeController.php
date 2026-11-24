<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $projects = Project::ordered()->get();
        $skills = Skill::orderBy('order')->get();
        $professional = Experience::where('kind', '!=', 'education');
        $firstExperience = (clone $professional)->orderBy('started_at')->first();

        return Inertia::render('Home', [
            // Rangée de 3 valeurs du hero : chiffres + détail, calculés depuis la base
            'highlights' => [
                'projects' => [
                    'count' => $projects->count(),
                    'detail' => $projects->pluck('category')->unique()->values(),
                ],
                'skills' => [
                    'count' => $skills->count(),
                    'detail' => $skills->pluck('category')->unique()->values(),
                ],
                'experiences' => [
                    'count' => $professional->count(),
                    'since' => $firstExperience?->started_at->year,
                ],
            ],
            'services' => Service::orderBy('order')->get()->map->only(['id', 'title', 'description', 'icon'])->values(),
            'featuredProjects' => $projects->take(3)->values(),
        ]);
    }
}
