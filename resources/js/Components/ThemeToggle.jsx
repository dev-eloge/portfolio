import { IconMoon, IconSun } from '@tabler/icons-react';
import { useEffect, useState } from 'react';
import useTrans from '@/lib/useTrans';

export default function ThemeToggle() {
    const [dark, setDark] = useState(false);
    const { t } = useTrans();

    useEffect(() => {
        setDark(document.documentElement.classList.contains('dark'));
    }, []);

    const toggle = () => {
        const next = !dark;
        document.documentElement.classList.toggle('dark', next);
        try {
            localStorage.setItem('theme', next ? 'dark' : 'light');
        } catch (e) {
            // stockage indisponible : le thème reste valable pour cette visite
        }
        setDark(next);
    };

    return (
        <button
            type="button"
            onClick={toggle}
            aria-label={dark ? t('Activer le mode clair') : t('Activer le mode sombre')}
            className="grid size-11 place-items-center rounded-full border-2 border-ink text-ink transition-colors hover:bg-surface focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
        >
            {dark ? <IconSun size={20} aria-hidden="true" /> : <IconMoon size={20} aria-hidden="true" />}
        </button>
    );
}
