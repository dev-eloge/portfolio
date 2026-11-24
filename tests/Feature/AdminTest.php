<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use App\Support\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Profile::flush();
    }

    private function admin(): User
    {
        return User::factory()->create(['email' => 'admin@example.com', 'password' => bcrypt('mot-de-passe-solide')]);
    }

    public function test_visiteur_redirige_vers_la_connexion_et_pages_non_indexees(): void
    {
        $this->get('/admin')->assertRedirect('/admin/connexion');
        $this->get('/admin/projets')->assertRedirect('/admin/connexion');
        $this->get('/admin/connexion')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_connexion_et_limite_de_tentatives(): void
    {
        $this->admin();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/connexion', ['email' => 'admin@example.com', 'password' => 'faux'])->assertSessionHasErrors('email');
        }
        $this->post('/admin/connexion', ['email' => 'admin@example.com', 'password' => 'mot-de-passe-solide'])
            ->assertSessionHasErrors('email');
        $this->assertStringContainsString('Trop de tentatives', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_connexion_reussie(): void
    {
        $this->admin();

        $this->post('/admin/connexion', ['email' => 'admin@example.com', 'password' => 'mot-de-passe-solide'])->assertRedirect('/admin');
        $this->assertAuthenticated();
        $this->get('/admin')->assertOk()->assertInertia(fn (Assert $p) => $p->component('Admin/Dashboard'));
    }

    public function test_creer_un_projet_avec_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/projets', [
            'title' => ['fr' => 'Mon Projet', 'en' => 'My Project'],
            'category' => ['fr' => 'Application web'],
            'description' => ['fr' => 'Une description.'],
            'technologies' => 'Laravel, React , ',
            'url_live' => 'https://example.com',
            'image' => UploadedFile::fake()->image('capture.png', 1200, 900),
            'featured' => true,
            'order' => 2,
        ])->assertRedirect('/admin/projets')->assertSessionHasNoErrors();

        $project = Project::firstWhere('slug', 'mon-projet');
        $this->assertSame('application-web', $project->category_slug);
        $this->assertSame(['Laravel', 'React'], $project->technologies);
        $this->assertSame('My Project', $project->getTranslation('title', 'en'));
        $this->assertStringStartsWith('/storage/uploads/projects/', $project->image);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->image));
    }

    public function test_validation_francais_obligatoire(): void
    {
        $this->actingAs($this->admin())->post('/admin/projets', ['title' => ['en' => 'Only English']])
            ->assertSessionHasErrors(['title.fr', 'category.fr', 'description.fr']);
    }

    public function test_parcours_en_cours_et_competence_traduite(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/parcours', [
            'kind' => 'work', 'title' => ['fr' => 'Développeur'], 'started_year' => 2026, 'ended_year' => '',
        ])->assertSessionHasNoErrors();
        $this->assertNull(Experience::first()->ended_at);
        $this->assertSame(2026, Experience::first()->started_at->year);

        $this->post('/admin/competences', ['group' => 'Outils', 'name' => ['fr' => 'Docker']])->assertSessionHasNoErrors();
        $this->assertSame('Tools', Skill::first()->getTranslation('category', 'en'));
    }

    public function test_profil_modifie_visible_sur_le_site(): void
    {
        $this->actingAs($this->admin())->put('/admin/profil', [
            'name' => 'Eloge',
            'role' => ['fr' => 'Développeur fullstack', 'en' => 'Full-stack developer'],
            'intro' => ['fr' => 'Nouvelle présentation.'],
            'about' => ['fr' => "Premier paragraphe.\n\nDeuxième paragraphe."],
            'github' => 'https://github.com/dev-eloge',
        ])->assertSessionHasNoErrors();

        Profile::flush();
        $this->get('/')->assertInertia(fn (Assert $p) => $p
            ->where('profile.intro', 'Nouvelle présentation.')
            ->where('profile.about', ['Premier paragraphe.', 'Deuxième paragraphe.'])
            ->where('profile.role', 'Développeur fullstack'));
    }

    public function test_messages_lecture_et_suppression(): void
    {
        $message = ContactMessage::create(['name' => 'Awa', 'email' => 'awa@example.com', 'message' => 'Bonjour, un devis ?']);

        $this->actingAs($this->admin())->get("/admin/messages/{$message->id}")->assertOk();
        $this->assertNotNull($message->fresh()->read_at);

        $this->delete("/admin/messages/{$message->id}")->assertRedirect('/admin/messages');
        $this->assertDatabaseCount(ContactMessage::class, 0);
    }
}
