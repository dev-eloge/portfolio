<?php

namespace Tests\Feature;

use App\Models\Certification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CertificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_page_certifications(): void
    {
        $this->seed();

        $this->get('/certifications')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Certifications')
            ->has('certifications', Certification::count()));
    }

    public function test_lien_du_menu_seulement_s_il_y_a_des_certifications(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('hasCertifications', false));

        Certification::create(['title' => ['fr' => 'Test'], 'issuer' => 'Org', 'issued_at' => '2024-01-01']);

        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('hasCertifications', true));
    }

    public function test_certification_expiree_et_traduction(): void
    {
        Certification::create([
            'title' => ['fr' => 'Certification FR', 'en' => 'Certification EN'],
            'issuer' => 'Org',
            'issued_at' => '2020-01-01',
            'expires_at' => '2021-01-01',
        ]);

        $this->withSession(['locale' => 'en'])->get('/certifications')->assertInertia(fn (Assert $page) => $page
            ->where('certifications.0.title', 'Certification EN')
            ->where('certifications.0.expired', true)
            ->where('certifications.0.issued', 'January 2020'));
    }
}
