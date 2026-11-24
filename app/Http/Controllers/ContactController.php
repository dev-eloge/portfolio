<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.required' => __('Indiquez votre nom.'),
            'name.max' => __('Le nom ne doit pas dépasser 100 caractères.'),
            'email.required' => __('Indiquez votre adresse e-mail.'),
            'email.email' => __("L'adresse e-mail n'est pas valide."),
            'email.max' => __("L'adresse e-mail ne doit pas dépasser 150 caractères."),
            'subject.max' => __('Le sujet ne doit pas dépasser 150 caractères.'),
            'message.required' => __('Écrivez votre message.'),
            'message.min' => __('Le message doit contenir au moins 10 caractères.'),
            'message.max' => __('Le message ne doit pas dépasser 5000 caractères.'),
        ]);

        $contactMessage = ContactMessage::create($data);

        $this->notifyOwner($contactMessage);

        return back()->with('success', __('Message envoyé. Je vous réponds rapidement.'));
    }

    /**
     * Prévient le propriétaire par e-mail. Un échec d'envoi n'empêche pas
     * l'enregistrement : le message reste dans la table contact_messages.
     */
    private function notifyOwner(ContactMessage $contactMessage): void
    {
        $recipient = config('services.contact.notification_email');

        if (blank($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new ContactMessageReceived($contactMessage));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
