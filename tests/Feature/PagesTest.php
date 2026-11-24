<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_les_pages_publiques_repondent(): void
    {
        $this->seed();

        foreach (['/', '/a-propos', '/projets', '/competences', '/experience', '/contact'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_detail_projet_et_404(): void
    {
        $this->seed();
        $project = Project::first();

        $this->get("/projets/{$project->slug}")
            ->assertInertia(fn (Assert $page) => $page->component('Projects/Show')->where('project.slug', $project->slug));

        $this->get('/projets/inexistant')->assertNotFound();
    }

    public function test_filtre_par_categorie(): void
    {
        $this->seed();
        $expected = Project::where('category_slug', 'intelligence-artificielle')->count();

        $this->get('/projets?category=intelligence-artificielle')
            ->assertInertia(fn (Assert $page) => $page->has('projects', $expected));
    }

    public function test_competences_groupees_dans_l_ordre(): void
    {
        Skill::create(['name' => 'Git', 'category' => 'Outils']);
        Skill::create(['name' => 'Laravel', 'category' => 'Backend']);

        $this->get('/competences')->assertInertia(fn (Assert $page) => $page
            ->where('skillGroups.0.category', 'Backend')
            ->where('skillGroups.1.category', 'Outils'));
    }

    public function test_experience_en_cours_en_premier(): void
    {
        Experience::create(['title' => 'Ancien poste', 'started_at' => '2020-01-01', 'ended_at' => '2021-01-01']);
        Experience::create(['title' => 'Poste actuel', 'started_at' => '2022-01-01']);

        $this->get('/experience')->assertInertia(fn (Assert $page) => $page
            ->where('experiences.0.title', 'Poste actuel')
            ->where('experiences.0.current', true));
    }

    public function test_formulaire_contact_enregistre_le_message(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => 'Awa',
            'email' => 'awa@example.com',
            'message' => 'Bonjour, je voudrais un devis.',
        ])->assertRedirect('/contact')->assertSessionHas('success');

        $this->assertDatabaseHas(ContactMessage::class, ['email' => 'awa@example.com']);
    }

    public function test_formulaire_contact_valide_les_champs(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => '',
            'email' => 'pas-un-email',
            'message' => 'court',
        ])->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount(ContactMessage::class, 0);
    }

    public function test_parcours_formation_separee_et_periodes_a_l_annee(): void
    {
        $this->seed();

        $this->get('/a-propos')->assertInertia(fn (Assert $page) => $page
            ->has('education', 2)
            ->where('education.0.title', 'Licence en génie logiciel')
            ->where('education.0.period', "2023 – Aujourd'hui")
            ->has('experiences', 4));

        $this->get('/experience')->assertInertia(fn (Assert $page) => $page
            ->has('experiences', 6)
            ->where('experiences.0.title', 'Développeur fullstack')
            ->where('experiences.0.period', "2026 – Aujourd'hui"));

        // Le bac et la licence ne comptent pas comme « expériences » dans le hero
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('highlights.experiences.count', 4)
            ->where('highlights.experiences.since', 2024));
    }
}
