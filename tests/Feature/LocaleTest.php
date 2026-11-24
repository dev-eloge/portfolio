<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_francais_par_defaut(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('locale', 'fr')
            ->where('translations', [])
            ->where('profile.role', 'Développeur fullstack IA'));
    }

    public function test_bascule_en_anglais_et_retour_sur_la_meme_page(): void
    {
        $this->from('/projets')->get('/langue/en')->assertRedirect('/projets');

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('locale', 'en')
            ->where('translations.Accueil', 'Home')
            ->where('profile.role', 'Full-stack AI developer'));
    }

    public function test_langue_inconnue_refusee(): void
    {
        $this->get('/langue/de')->assertNotFound();
    }

    public function test_redirection_externe_ignoree(): void
    {
        $this->from('https://site-externe.test/page')->get('/langue/en')->assertRedirect(url('/'));
    }

    public function test_contenu_traduit_avec_repli_sur_le_francais(): void
    {
        Project::create([
            'title' => ['fr' => 'Projet FR', 'en' => 'Project EN'],
            'slug' => 'p',
            'category' => ['fr' => 'Web'],
            'category_slug' => 'web',
            'description' => ['fr' => 'Description seulement en français'],
        ]);

        $this->withSession(['locale' => 'en'])->get('/projets/p')->assertInertia(fn (Assert $page) => $page
            ->where('project.title', 'Project EN')
            ->where('project.description', 'Description seulement en français'));
    }

    public function test_messages_de_validation_traduits(): void
    {
        $this->withSession(['locale' => 'en'])
            ->from('/contact')
            ->post('/contact', ['name' => '', 'email' => 'x', 'message' => 'court'])
            ->assertSessionHasErrors(['name' => 'Please enter your name.']);
    }
}
