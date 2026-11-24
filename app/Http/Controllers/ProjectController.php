<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->string('category')->toString() ?: null;

        $projects = Project::ordered()
            ->when($category, fn ($query) => $query->where('category_slug', $category))
            ->get();

        $categories = Project::query()->get(['category', 'category_slug'])
            ->unique('category_slug')
            ->map(fn (Project $p) => ['category' => $p->category, 'category_slug' => $p->category_slug])
            ->sortBy('category')
            ->values();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'categories' => $categories,
            'activeCategory' => $category,
            'total' => Project::count(),
        ]);
    }

    public function show(Project $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }
}
