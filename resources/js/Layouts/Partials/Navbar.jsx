import { Link, usePage } from '@inertiajs/react';
import { IconDownload, IconMenu2, IconX } from '@tabler/icons-react';
import { useEffect, useState } from 'react';
import Button from '@/Components/Button';
import LanguageSwitch from '@/Components/LanguageSwitch';
import ThemeToggle from '@/Components/ThemeToggle';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const links = [
    { href: '/', label: 'Accueil' },
    { href: '/a-propos', label: 'À propos' },
    { href: '/projets', label: 'Projets' },
    { href: '/competences', label: 'Compétences' },
    { href: '/experience', label: 'Expérience' },
    { href: '/certifications', label: 'Certifications', requires: 'hasCertifications' },
];

function isActive(url, href) {
    const path = url.split('?')[0];
    return href === '/' ? path === '/' : path.startsWith(href);
}

export default function Navbar() {
    const { url, props } = usePage();
    const { profile } = props;
    const visibleLinks = links.filter((link) => !link.requires || props[link.requires]);
    const [open, setOpen] = useState(false);
    const { t } = useTrans();

    useEffect(() => setOpen(false), [url]);

    useEffect(() => {
        document.body.style.overflow = open ? 'hidden' : '';
        return () => {
            document.body.style.overflow = '';
        };
    }, [open]);

    const focusRing = 'focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent';

    return (
        <header className="sticky top-0 z-50 border-b-2 border-ink bg-paper">
            <nav className="mx-auto flex h-18 max-w-6xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8" aria-label={t('Navigation principale')}>
                <Link href="/" className={cn('whitespace-nowrap font-display text-xl uppercase sm:text-2xl', focusRing)}>
                    {profile.name}
                </Link>

                <ul className="hidden items-center gap-5 xl:flex">
                    {visibleLinks.map((link) => (
                        <li key={link.href}>
                            <Link
                                href={link.href}
                                aria-current={isActive(url, link.href) ? 'page' : undefined}
                                className={cn(
                                    'whitespace-nowrap py-2 text-sm font-bold uppercase underline-offset-8 decoration-accent decoration-[3px]',
                                    isActive(url, link.href) ? 'underline' : 'hover:underline',
                                    focusRing,
                                )}
                            >
                                {t(link.label)}
                            </Link>
                        </li>
                    ))}
                </ul>

                <div className="flex items-center gap-3">
                    <LanguageSwitch className="hidden sm:flex" />
                    <ThemeToggle />
                    {profile.cv && (
                        <a href={profile.cv} download={`CV-${profile.name.replace(/\s+/g, '-')}.pdf`} className={cn('hidden min-h-11 items-center gap-2 px-2 font-bold sm:inline-flex', focusRing)}>
                            <IconDownload size={20} aria-hidden="true" />
                            CV
                        </a>
                    )}
                    <div className="hidden md:block">
                        <Button href="/contact">{t('Me contacter')}</Button>
                    </div>
                    <button
                        type="button"
                        onClick={() => setOpen(!open)}
                        aria-expanded={open}
                        aria-controls="mobile-menu"
                        aria-label={open ? t('Fermer le menu') : t('Ouvrir le menu')}
                        className={cn('grid size-11 place-items-center border-2 border-ink xl:hidden', focusRing)}
                    >
                        {open ? <IconX size={22} aria-hidden="true" /> : <IconMenu2 size={22} aria-hidden="true" />}
                    </button>
                </div>
            </nav>

            {open && (
                <div id="mobile-menu" className="fixed inset-x-0 bottom-0 top-[74px] z-50 overflow-y-auto bg-paper px-4 py-10 sm:px-6 xl:hidden">
                    <ul className="space-y-4">
                        {visibleLinks.map((link) => (
                            <li key={link.href}>
                                <Link
                                    href={link.href}
                                    aria-current={isActive(url, link.href) ? 'page' : undefined}
                                    className={cn(
                                        'whitespace-nowrap font-display text-4xl uppercase decoration-accent decoration-4 underline-offset-8',
                                        isActive(url, link.href) && 'underline',
                                        focusRing,
                                    )}
                                >
                                    {t(link.label)}
                                </Link>
                            </li>
                        ))}
                    </ul>
                    <LanguageSwitch className="mt-10 w-fit sm:hidden" />
                    <div className="mt-10 flex flex-col items-start gap-4">
                        <Button href="/contact">{t('Me contacter')}</Button>
                        {profile.cv && (
                            <Button href={profile.cv} variant="link" download={`CV-${profile.name.replace(/\s+/g, '-')}.pdf`}>
                                {t('Télécharger le CV')}
                            </Button>
                        )}
                    </div>
                </div>
            )}
        </header>
    );
}
