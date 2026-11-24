<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\PortfolioAssistant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Tests\TestCase;

class AssistantTest extends TestCase
{
    use RefreshDatabase;

    private array $question = ['messages' => [['role' => 'user', 'content' => 'Qui est Eloge ?']]];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config(['services.anthropic.key' => 'test-key']);
    }

    public function test_assistant_masque_sans_cle_api(): void
    {
        config(['services.anthropic.key' => null]);

        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('assistant', null));
        $this->postJson('/assistant', $this->question)->assertStatus(503);
    }

    public function test_assistant_visible_avec_cle_api(): void
    {
        $this->seed();

        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('assistant.projects', Project::count()));
    }

    public function test_reponse_de_l_assistant(): void
    {
        $this->mock(PortfolioAssistant::class)
            ->shouldReceive('ask')
            ->once()
            ->with($this->question['messages'], null)
            ->andReturn(['answer' => 'Eloge est développeur fullstack.', 'refused' => false]);

        $this->postJson('/assistant', $this->question)
            ->assertOk()
            ->assertJson(['answer' => 'Eloge est développeur fullstack.']);
    }

    public function test_question_sur_un_projet_transmet_le_projet(): void
    {
        $this->seed();
        $project = Project::first();

        $this->mock(PortfolioAssistant::class)
            ->shouldReceive('ask')
            ->once()
            ->with(Mockery::any(), Mockery::on(fn ($p) => $p?->is($project)))
            ->andReturn(['answer' => 'Explication.', 'refused' => false]);

        $this->postJson('/assistant', [...$this->question, 'project' => $project->slug])->assertOk();
    }

    public function test_refus_renvoie_un_message_neutre(): void
    {
        $this->mock(PortfolioAssistant::class)
            ->shouldReceive('ask')
            ->andReturn(['answer' => '', 'refused' => true]);

        $this->postJson('/assistant', $this->question)
            ->assertOk()
            ->assertJsonPath('answer', fn ($answer) => str_starts_with($answer, 'Je ne peux pas répondre'));
    }

    public function test_validation_des_entrees(): void
    {
        $this->postJson('/assistant', ['messages' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['messages']);

        $this->postJson('/assistant', [...$this->question, 'project' => 'inexistant'])
            ->assertJsonValidationErrors(['project']);
    }

    public function test_la_conversation_doit_finir_par_le_visiteur(): void
    {
        $this->postJson('/assistant', [
            'messages' => [['role' => 'user', 'content' => 'Bonjour'], ['role' => 'assistant', 'content' => 'Bonjour !']],
        ])->assertStatus(422);
    }

    public function test_le_prompt_contient_les_donnees_dans_la_langue_active(): void
    {
        $this->seed();
        $assistant = app(PortfolioAssistant::class);

        $this->assertStringContainsString('Application web', $assistant->knowledgePrompt());
        $this->assertStringContainsString('Answer in French', $assistant->knowledgePrompt());

        app()->setLocale('en');
        $prompt = $assistant->knowledgePrompt();
        $this->assertStringContainsString('Web application', $prompt);
        $this->assertStringContainsString('Answer in English', $prompt);
        $this->assertStringNotContainsString('"fr":', $prompt);
    }
}
