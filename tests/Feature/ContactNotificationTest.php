<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class ContactNotificationTest extends TestCase
{
    use RefreshDatabase;

    private array $form = [
        'name' => 'Awa',
        'email' => 'awa@example.com',
        'subject' => 'Devis',
        'message' => 'Bonjour, je voudrais un devis pour un site.',
    ];

    public function test_e_mail_envoye_au_proprietaire_avec_reponse_a_l_expediteur(): void
    {
        Mail::fake();
        config(['services.contact.notification_email' => 'moi@example.com']);

        $this->post('/contact', $this->form)->assertSessionHas('success');

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('moi@example.com')
                && $mail->hasReplyTo('awa@example.com', 'Awa')
                && $mail->hasSubject('Nouveau message du portfolio : Devis');
        });
    }

    public function test_aucun_e_mail_sans_adresse_configuree(): void
    {
        Mail::fake();
        config(['services.contact.notification_email' => null]);

        $this->post('/contact', $this->form)->assertSessionHas('success');

        Mail::assertNothingSent();
        $this->assertDatabaseCount(ContactMessage::class, 1);
    }

    public function test_un_echec_d_envoi_n_empeche_pas_l_enregistrement(): void
    {
        config(['services.contact.notification_email' => 'moi@example.com']);
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP indisponible'));

        $this->post('/contact', $this->form)->assertSessionHas('success');

        $this->assertDatabaseHas(ContactMessage::class, ['email' => 'awa@example.com']);
    }

    public function test_contenu_de_l_e_mail(): void
    {
        $message = ContactMessage::create($this->form);

        (new ContactMessageReceived($message))
            ->assertSeeInHtml('Awa')
            ->assertSeeInHtml('awa@example.com')
            ->assertSeeInHtml('je voudrais un devis')
            ->assertSeeInHtml('Répondre à Awa');
    }

    public function test_l_adresse_de_notification_n_est_pas_exposee_au_navigateur(): void
    {
        $this->withoutVite();
        config(['services.contact.notification_email' => 'secret@example.com']);

        $this->get('/contact')->assertDontSee('secret@example.com');
    }
}
