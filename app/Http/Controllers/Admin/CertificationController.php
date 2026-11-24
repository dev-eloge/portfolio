<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CertificationController extends ResourceController
{
    protected string $route = 'certifications';

    protected string $title = 'Certifications';

    protected string $singular = 'Certification';

    protected function model(): string
    {
        return Certification::class;
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'type' => 'translatable', 'label' => 'Intitulé', 'required' => true],
            ['name' => 'issuer', 'type' => 'text', 'label' => 'Organisme', 'required' => true],
            ['name' => 'issued_at', 'type' => 'date', 'label' => "Date d'obtention", 'required' => true],
            ['name' => 'expires_at', 'type' => 'date', 'label' => "Date d'expiration", 'help' => 'Vide = sans expiration.'],
            ['name' => 'credential_id', 'type' => 'text', 'label' => 'Identifiant du certificat'],
            ['name' => 'credential_url', 'type' => 'url', 'label' => 'Lien de vérification'],
            ['name' => 'skills', 'type' => 'tags', 'label' => 'Compétences validées', 'help' => 'Séparées par des virgules.'],
            ['name' => 'order', 'type' => 'number', 'label' => "Ordre d'affichage", 'default' => 0],
        ];
    }

    protected function columns(): array
    {
        return ['Intitulé', 'Organisme', 'Obtenue le'];
    }

    protected function row(Model $m): array
    {
        return [$m->getTranslation('title', 'fr'), $m->issuer, $m->issued_at->format('d/m/Y')];
    }

    protected function query(): Builder
    {
        return Certification::ordered();
    }
}
