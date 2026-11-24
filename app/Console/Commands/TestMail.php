<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

/** Envoie un e-mail de test pour vérifier la configuration Gmail (SMTP). */
class TestMail extends Command
{
    protected $signature = 'portfolio:test-mail {to? : Destinataire (par défaut : CONTACT_NOTIFICATION_EMAIL)}';

    protected $description = "Teste l'envoi d'e-mails (configuration MAIL_* du .env)";

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('services.contact.notification_email');

        if (blank($to)) {
            $this->error('Aucun destinataire : renseignez CONTACT_NOTIFICATION_EMAIL dans .env ou passez une adresse.');

            return self::FAILURE;
        }

        if (config('mail.default') === 'smtp' && blank(config('mail.mailers.smtp.password'))) {
            $this->error("MAIL_PASSWORD est vide dans .env : collez-y le mot de passe d'application Google.");

            return self::FAILURE;
        }

        $this->line("Envoi via « ".config('mail.default')." » (".config('mail.mailers.smtp.host').") vers {$to}…");

        try {
            Mail::raw("Ceci est un e-mail de test envoyé par ton portfolio.\nSi tu le reçois, les notifications du formulaire de contact fonctionnent.", function ($message) use ($to) {
                $message->to($to)->subject('Test d’envoi — portfolio');
            });
        } catch (Throwable $e) {
            $this->error('Échec : '.$e->getMessage());
            if (str_contains($e->getMessage(), '535') || str_contains(strtolower($e->getMessage()), 'username and password')) {
                $this->warn("Gmail refuse l'identification : vérifie MAIL_USERNAME et utilise un mot de passe d'application (pas ton mot de passe Gmail), collé sans espaces.");
            }

            return self::FAILURE;
        }

        $this->info('E-mail envoyé. Vérifie ta boîte de réception (et les spams).');

        return self::SUCCESS;
    }
}
