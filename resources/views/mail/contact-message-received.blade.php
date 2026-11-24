<x-mail::message>
# Nouveau message depuis le portfolio

**De :** {{ $contactMessage->name }} ({{ $contactMessage->email }})
@if ($contactMessage->subject)
**Sujet :** {{ $contactMessage->subject }}
@endif
**Reçu le :** {{ $contactMessage->created_at->locale('fr')->translatedFormat('j F Y à H:i') }}

<x-mail::panel>
{!! nl2br(e($contactMessage->message)) !!}
</x-mail::panel>

<x-mail::button :url="'mailto:' . $contactMessage->email . '?subject=' . rawurlencode('Re: ' . ($contactMessage->subject ?: 'votre message'))">
Répondre à {{ $contactMessage->name }}
</x-mail::button>

Vous pouvez aussi répondre directement à cet e-mail.
</x-mail::message>
