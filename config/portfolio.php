<?php

/*
|--------------------------------------------------------------------------
| Profil du portfolio
|--------------------------------------------------------------------------
| Informations personnelles affichées sur le site. Une valeur `null` ou un
| tableau vide masque l'élément correspondant.
|
| Textes bilingues : ['fr' => '...', 'en' => '...']. La bonne langue est
| choisie automatiquement (voir HandleInertiaRequests::localize()).
*/

return [

    // Adresse de l'admin (ex. ADMIN_PATH=gestion-eloge dans .env pour une adresse moins évidente)
    'admin_path' => env('ADMIN_PATH', 'admin'),

    'name' => 'Eloge HEMAZRO',

    // Sur-titre du hero
    'role' => ['fr' => 'Développeur fullstack IA', 'en' => 'Full-stack AI developer'],

    // Courte présentation du hero (affichée en effet machine à écrire)
    'intro' => [
        'fr' => "Je conçois des applications web complètes, de la base de données à la mise en ligne, avec l'IA comme outil de travail au quotidien.",
        'en' => 'I build complete web applications, from the database to deployment, with AI as an everyday working tool.',
    ],

    // Texte de la page À propos, un paragraphe par entrée
    'about' => [
        'fr' => [
            "Je suis développeur fullstack et je termine ma licence en génie logiciel. Je construis des applications web de bout en bout : la base de données, l'interface, puis la mise en ligne sur un serveur.",
            "J'ai commencé par le design, en stage chez Orga Africa, puis j'ai enchaîné les projets : Clever-Green lors d'un hackathon, Miabe-School en projet personnel, et aujourd'hui CODCompta, une application en ligne pour les e-commerçants en paiement à la livraison.",
            "L'IA fait partie de ma façon de travailler. J'aime l'utiliser au quotidien : je code avec Claude Code, et je crée et entraîne mes propres agents IA sur différents modèles.",
        ],
        'en' => [
            "I'm a full-stack developer finishing my bachelor's degree in software engineering. I build web applications end to end: the database, the interface, then deployment on a server.",
            'I started out in design, as an intern at Orga Africa, then moved from project to project: Clever-Green at a hackathon, Miabe-School as a personal project, and now CODCompta, an online app for cash-on-delivery e-merchants.',
            'AI is part of how I work. I love using it every day: I code with Claude Code, and I build and train my own AI agents on different models.',
        ],
    ],

    // Localisation et mode de travail
    'location' => 'Lomé',
    'work_mode' => ['fr' => 'Remote / hybride', 'en' => 'Remote / hybrid'],

    'images' => [
        // Visage et épaules recadrés en rond pour le hero (360×360, recadré depuis eloge-portrait-face.jpeg)
        'face' => '/images/eloge-face.webp',
        'face_alt' => ['fr' => "Visage d'Eloge, souriant, avec une casquette grise", 'en' => 'Eloge smiling, wearing a grey cap'],
        'portrait' => '/images/eloge-portrait-face.webp',
        'portrait_alt' => ['fr' => "Portrait d'Eloge, souriant, sur une plage", 'en' => 'Portrait of Eloge smiling on a beach'],
        'about' => '/images/eloge-portrait-profil.webp',
        'about_alt' => ['fr' => 'Eloge de trois-quarts, regardant vers la droite, sur une plage', 'en' => 'Eloge in three-quarter view, looking to the right, on a beach'],
    ],

    // Chemin du CV dans public/ (ex. '/cv/eloge-cv.pdf') — À AJOUTER
    'cv' => null,

    // E-mail public (footer, page Contact, assistant IA)
    'email' => 'elogehemazro42@gmail.com',

    // Liens
    'github' => 'https://github.com/dev-eloge',
    'linkedin' => 'https://www.linkedin.com/in/eloge-hemazro',

    /*
    | Services « Ce que je peux construire avec vous » (confirmés par Eloge).
    | Icônes disponibles : code, brain, layout, database, api, device-mobile.
    */
    'services' => [
        [
            'title' => ['fr' => 'Applications web sur mesure', 'en' => 'Custom web applications'],
            'description' => [
                'fr' => 'Back-office, espace client, plateforme de réservation : une application Laravel + React adaptée à votre façon de travailler.',
                'en' => 'Back office, customer portal, booking platform: a Laravel + React application shaped around the way you work.',
            ],
            'icon' => 'layout',
        ],
        [
            'title' => ['fr' => 'Agents IA sur mesure', 'en' => 'Custom AI agents'],
            'description' => [
                'fr' => "Je crée et j'entraîne des agents IA adaptés à votre activité, sur le modèle qui convient le mieux à la tâche.",
                'en' => 'I build and train AI agents tailored to your business, on whichever model best fits the task.',
            ],
            'icon' => 'brain',
        ],
        [
            'title' => ['fr' => 'API et intégrations', 'en' => 'APIs and integrations'],
            'description' => [
                'fr' => 'Connexion de vos outils entre eux : paiement mobile, CRM, e-mails, webhooks.',
                'en' => 'Connecting your tools together: mobile payments, CRM, email, webhooks.',
            ],
            'icon' => 'api',
        ],
        [
            'title' => ['fr' => 'Reprise de projet existant', 'en' => 'Taking over existing projects'],
            'description' => [
                'fr' => 'Audit, correction de bugs, montée de version Laravel et amélioration des performances.',
                'en' => 'Audit, bug fixing, Laravel upgrades and performance improvements.',
            ],
            'icon' => 'code',
        ],
    ],

];
