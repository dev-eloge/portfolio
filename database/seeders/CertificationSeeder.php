<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

/**
 * Aucune certification pour l'instant (communiqué par Eloge le 23/11/2026).
 * Tant que la table est vide, le lien « Certifications » n'apparaît pas dans le menu.
 *
 * Format d'une entrée :
 * [
 *     'title' => ['fr' => '...', 'en' => '...'],
 *     'issuer' => 'Organisme',
 *     'issued_at' => '2027-01-15',
 *     'expires_at' => null,           // null = sans expiration
 *     'credential_id' => null,
 *     'credential_url' => 'https://...',
 *     'skills' => ['Laravel'],
 *     'order' => 1,
 * ],
 */
class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [];

        Certification::query()->delete();

        foreach ($certifications as $certification) {
            Certification::create($certification);
        }
    }
}
