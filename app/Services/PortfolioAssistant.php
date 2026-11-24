<?php

namespace App\Services;

use Anthropic\Client;
use App\Models\Certification;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Support\Localize;
use App\Support\Profile;

/**
 * Assistant IA du portfolio : répond uniquement à partir des données du site.
 */
class PortfolioAssistant
{
    public function __construct(private readonly Client $client) {}

    public static function enabled(): bool
    {
        return filled(config('services.anthropic.key'));
    }

    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $messages
     * @return array{answer: string, refused: bool}
     */
    public function ask(array $messages, ?Project $project = null): array
    {
        $response = $this->client->beta->messages->create(
            model: config('services.anthropic.model'),
            maxTokens: 8000,
            system: [
                // Bloc stable (règles + données du site) : mis en cache
                ['type' => 'text', 'text' => $this->knowledgePrompt(), 'cacheControl' => ['type' => 'ephemeral']],
                // Bloc variable (projet éventuel) : après le point de cache
                ['type' => 'text', 'text' => $this->contextPrompt($project)],
            ],
            messages: $messages,
            // Conversation simple : effort bas = réponses rapides et moins coûteuses
            outputConfig: ['effort' => 'low'],
            // Si le modèle refuse pour raison de sécurité, l'API réessaie sur un modèle de repli
            betas: ['server-side-fallback-2026-07-01'],
            fallbacks: 'default',
        );

        if ($response->stopReason === 'refusal') {
            return ['answer' => '', 'refused' => true];
        }

        $answer = '';
        foreach ($response->content as $block) {
            if ($block->type === 'text') {
                $answer .= $block->text;
            }
        }

        return ['answer' => trim($answer), 'refused' => false];
    }

    /** Règles + toutes les données publiques du site, dans la langue active. */
    public function knowledgePrompt(): string
    {
        $locale = app()->getLocale();
        $language = $locale === 'en' ? 'English' : 'French';
        $profile = Localize::value(Profile::all());

        $data = [
            'profile' => collect($profile)->only(['name', 'role', 'intro', 'about', 'location', 'work_mode', 'email', 'github', 'linkedin'])->all(),
            'services' => Service::orderBy('order')->get()->map(fn (Service $s) => ['title' => $s->title, 'description' => $s->description])->all(),
            'skills' => Skill::orderBy('order')->get()
                ->groupBy(fn (Skill $s) => $s->category)
                ->map(fn ($skills) => $skills->pluck('name')->values())
                ->all(),
            'experiences' => Experience::orderByRaw('ended_at IS NOT NULL')->orderByDesc('started_at')->get()
                ->map(fn (Experience $e) => [
                    'kind' => $e->kind, // education, work ou project
                    'title' => $e->title,
                    'company' => $e->company,
                    'type' => $e->type,
                    // Seules les années sont connues
                    'from' => $e->started_at->format('Y'),
                    'to' => $e->ended_at?->format('Y') ?? 'present',
                    'description' => $e->description,
                ])->all(),
            'certifications' => Certification::ordered()->get()
                ->map(fn (Certification $c) => [
                    'title' => $c->title,
                    'issuer' => $c->issuer,
                    'issued' => $c->issued_at->format('Y-m'),
                    'expires' => $c->expires_at?->format('Y-m'),
                    'expired' => $c->isExpired(),
                    'credential_url' => $c->credential_url,
                    'skills' => $c->skills,
                ])->all(),
            'projects' => Project::ordered()->get()
                ->map(fn (Project $p) => [
                    'slug' => $p->slug,
                    'title' => $p->title,
                    'category' => $p->category,
                    'description' => $p->description,
                    'context' => $p->context,
                    'problem' => $p->problem,
                    'solution' => $p->solution,
                    'result' => $p->result,
                    'technologies' => $p->technologies,
                    'live_url' => $p->url_live,
                    'code_url' => $p->url_code,
                    'page' => url("/projets/{$p->slug}"),
                ])->all(),
        ];

        $name = $profile['name'];
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
        You are the assistant on {$name}'s portfolio website. Visitors are recruiters, potential clients and other developers who want to learn about {$name}'s work.

        Answer in {$language}, the language the visitor is browsing the site in, unless they write to you in another language.

        Base every answer only on the portfolio data below. It is the complete and only source of truth about {$name}. If a visitor asks about something the data does not cover (salary expectations, availability dates, a technology not listed, personal matters), say plainly that the portfolio doesn't say, and point them to the contact page at /contact. Never fill gaps with guesses: an invented skill or project would mislead a recruiter making a real decision.

        Speak about {$name} in the third person, warmly and factually, without hype. Prefer concrete facts from the data (what a project does, the problem it solved, the stack) over adjectives.

        Your answers are shown as plain text in a small chat panel, so write short paragraphs or simple lines starting with "- ". Do not use Markdown headings, bold, tables or code blocks. Keep answers to roughly 120 words unless the visitor asks for detail or the task needs more (such as comparing a job offer).

        If a request has nothing to do with {$name} or the portfolio, decline in one sentence and suggest a question you can help with.

        <portfolio_data>
        {$json}
        </portfolio_data>
        PROMPT;
    }

    public function contextPrompt(?Project $project = null): string
    {
        $prompt = 'Give clear overviews and point to the relevant pages (/projets, /a-propos, /competences, /experience, /certifications, /contact) when useful. When asked how a project was built, explain the problem, the approach and the stack using only what the project data says; never invent implementation details, libraries or numbers.';

        if ($project) {
            $prompt .= "\n\nThe visitor opened the assistant from the project \"{$project->title}\" (slug: {$project->slug}). Unless they ask about something else, their questions are about this project.";
        }

        return $prompt;
    }
}
