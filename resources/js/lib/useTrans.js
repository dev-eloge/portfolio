import { usePage } from '@inertiajs/react';

/**
 * Traduction des textes d'interface.
 * La clé est le texte français ; lang/en.json fournit l'anglais.
 * t('Bonjour :name', { name: 'Eloge' })
 */
export default function useTrans() {
    const { translations = {}, locale = 'fr' } = usePage().props;

    const t = (key, replace = {}) => {
        let text = translations[key] ?? key;
        for (const [name, value] of Object.entries(replace)) {
            text = text.replaceAll(`:${name}`, value);
        }
        return text;
    };

    return { t, locale };
}
