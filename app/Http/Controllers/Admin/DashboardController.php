<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'counts' => [
                ['label' => 'Projets', 'value' => Project::count(), 'href' => route('admin.projects.index')],
                ['label' => 'Parcours', 'value' => Experience::count(), 'href' => route('admin.experiences.index')],
                ['label' => 'Compétences', 'value' => Skill::count(), 'href' => route('admin.skills.index')],
                ['label' => 'Certifications', 'value' => Certification::count(), 'href' => route('admin.certifications.index')],
                ['label' => 'Services', 'value' => Service::count(), 'href' => route('admin.services.index')],
                ['label' => 'Messages non lus', 'value' => ContactMessage::whereNull('read_at')->count(), 'href' => route('admin.messages.index')],
            ],
            'latest' => ContactMessage::latest()->take(5)->get()->map(fn ($m) => [
                'id' => $m->id, 'name' => $m->name, 'subject' => $m->subject ?: str($m->message)->limit(50)->toString(),
                'date' => $m->created_at->format('d/m/Y'), 'read' => $m->read_at !== null,
            ]),
        ]);
    }
}
