<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Projets réels d'Eloge. Le seeder remplace tous les projets existants.
 * Chaque texte traduisible est un tableau ['fr' => ..., 'en' => ...].
 *
 * CODCompta : textes repris du site https://codcompta.africodehub.net/ (consulté le 23/11/2026).
 * Stack communiquée par Eloge. 'result' reste vide : aucun chiffre publié (ne rien inventer).
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => ['fr' => 'CODCompta', 'en' => 'CODCompta'],
                'slug' => 'codcompta',
                'category' => ['fr' => 'Application web', 'en' => 'Web application'],
                'category_slug' => 'application-web',
                'description' => [
                    'fr' => "La comptabilité du paiement à la livraison : l'e-commerçant colle les messages WhatsApp de ses agences et voit son bénéfice net, agence par agence.",
                    'en' => 'Accounting for cash-on-delivery sellers: the merchant pastes the WhatsApp messages from their agencies and sees their net profit, agency by agency.',
                ],
                'context' => [
                    'fr' => 'Les e-commerçants qui vendent en paiement à la livraison reçoivent les chiffres de leurs agences par messages WhatsApp.',
                    'en' => 'Cash-on-delivery e-merchants receive their agencies’ figures through WhatsApp messages.',
                ],
                'problem' => [
                    'fr' => "Recopier les chiffres un par un, puis retirer le prix d'achat, la publicité, le closing et la livraison : difficile de savoir chaque jour combien on gagne vraiment.",
                    'en' => 'Copying figures one by one, then subtracting purchase price, advertising, closing and delivery costs, makes it hard to know each day what you really earn.',
                ],
                'solution' => [
                    'fr' => "L'e-commerçant décrit une seule fois son activité (pays, agences, produits, prix), puis colle le message de l'agence tel quel : CODCompta lit les chiffres et il les vérifie. Il obtient son bénéfice net par agence, avec la publicité répartie entre les agences selon leurs ventes, des objectifs suivis par Sika, et un partage 40/20/40 pour épargner, se payer et réinvestir. L'application s'installe sur le téléphone sans passer par un store.",
                    'en' => 'The merchant describes their business once (countries, agencies, products, prices), then pastes the agency message as is: CODCompta reads the figures and the merchant checks them. They get their net profit per agency, with advertising split between agencies according to their sales, goals tracked by Sika, and a 40/20/40 split to save, pay themselves and reinvest. The app installs on the phone without going through a store.',
                ],
                'result' => null,
                'technologies' => ['Laravel', 'React', 'Tailwind CSS'],
                'url_live' => 'https://codcompta.africodehub.net/',
                'url_code' => null,
                'image' => '/images/projets/codcompta.webp',
                'image_alt' => [
                    'fr' => "Page d'accueil de CODCompta : « Sachez chaque jour combien vous gagnez vraiment », avec un exemple de bénéfice net par agence",
                    'en' => 'CODCompta home page: “Know every day how much you really earn”, with an example of net profit per agency',
                ],
                'featured' => true,
                'order' => 1,
            ],
        ];

        Project::whereNotIn('slug', array_column($projects, 'slug'))->delete();

        foreach ($projects as $project) {
            Project::updateOrCreate(['slug' => $project['slug']], $project);
        }
    }
}
