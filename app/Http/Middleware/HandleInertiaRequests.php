<?php

namespace App\Http\Middleware;

use App\Models\Certification;
use App\Models\Project;
use App\Services\PortfolioAssistant;
use App\Support\Localize;
use App\Support\Profile;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => $request->user() ? ['name' => $request->user()->name, 'email' => $request->user()->email] : null,
            'adminPath' => '/'.config('portfolio.admin_path'),
            'locale' => fn () => app()->getLocale(),
            'translations' => fn () => $this->translations(),
            'profile' => fn () => Localize::value(Profile::all()),
            // Le lien « Certifications » n'apparaît dans le menu que s'il y en a au moins une
            'hasCertifications' => fn () => Certification::query()->exists(),
            'assistant' => fn () => PortfolioAssistant::enabled() ? [
                'projects' => Project::ordered()->get(['slug', 'title'])
                    ->map(fn (Project $p) => ['slug' => $p->slug, 'title' => $p->title])
                    ->values(),
            ] : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }

    /**
     * Textes d'interface de la langue active (lang/{locale}.json).
     * Les clés sont les textes français : en français, rien n'est envoyé.
     */
    private function translations(): array
    {
        $path = lang_path(app()->getLocale().'.json');

        return is_file($path) ? json_decode(file_get_contents($path), true) : [];
    }
}
