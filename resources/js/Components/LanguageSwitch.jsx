import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const languages = [
    { code: 'fr', label: 'FR', name: 'Version française' },
    { code: 'en', label: 'EN', name: 'English version' },
];

/** Lien classique (rechargement complet) pour mettre à jour <html lang>. */
export default function LanguageSwitch({ className = '' }) {
    const { locale } = useTrans();

    return (
        <ul className={cn('flex border-2 border-ink font-mono text-sm font-bold', className)}>
            {languages.map((lang) => {
                const active = lang.code === locale;
                return (
                    <li key={lang.code}>
                        <a
                            href={`/langue/${lang.code}`}
                            hrefLang={lang.code}
                            lang={lang.code}
                            aria-label={lang.name}
                            aria-current={active ? 'true' : undefined}
                            className={cn(
                                'grid h-10 min-w-11 place-items-center px-2 transition-colors',
                                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent',
                                active ? 'bg-ink text-paper' : 'text-ink hover:bg-surface',
                            )}
                        >
                            {lang.label}
                        </a>
                    </li>
                );
            })}
        </ul>
    );
}
