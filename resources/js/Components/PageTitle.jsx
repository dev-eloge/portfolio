import { Head, usePage } from '@inertiajs/react';

/**
 * Titre de l'onglet du navigateur, construit avec le nom du profil (modifiable dans l'admin).
 *   Accueil (sans title) : « Eloge HEMAZRO — Développeur fullstack IA »
 *   Autres pages         : « Projets — Eloge HEMAZRO »
 */
export default function PageTitle({ title }) {
    const { profile } = usePage().props;
    const full = title ? `${title} — ${profile.name}` : [profile.name, profile.role].filter(Boolean).join(' — ');

    return <Head title={full} />;
}
