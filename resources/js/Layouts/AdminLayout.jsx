import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    IconBriefcase, IconCertificate, IconCode, IconExternalLink, IconLayoutDashboard, IconLogout, IconMail, IconRoute, IconTools, IconUser,
} from '@tabler/icons-react';
import { useEffect } from 'react';
import cn from '@/lib/cn';

const focusRing = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent';

export default function AdminLayout({ title, children }) {
    useAdminDarkMode();
    const { adminPath, flash, url } = { ...usePage().props, url: usePage().url };

    const links = [
        { href: adminPath, label: 'Tableau de bord', Icon: IconLayoutDashboard, exact: true },
        { href: `${adminPath}/profil`, label: 'Profil', Icon: IconUser },
        { href: `${adminPath}/projets`, label: 'Projets', Icon: IconCode },
        { href: `${adminPath}/parcours`, label: 'Parcours', Icon: IconRoute },
        { href: `${adminPath}/competences`, label: 'Compétences', Icon: IconTools },
        { href: `${adminPath}/certifications`, label: 'Certifications', Icon: IconCertificate },
        { href: `${adminPath}/services`, label: 'Services', Icon: IconBriefcase },
        { href: `${adminPath}/messages`, label: 'Messages', Icon: IconMail },
    ];
    const active = (l) => (l.exact ? url.split('?')[0] === l.href : url.startsWith(l.href));

    return (
        <div className="min-h-screen bg-paper text-ink lg:grid lg:grid-cols-[16rem_1fr]">
            <Head title={`${title} — Admin`}>
                <meta name="robots" content="noindex, nofollow" />
            </Head>
            <aside className="border-b-2 border-ink bg-surface lg:min-h-screen lg:border-b-0 lg:border-r-2">
                <div className="px-4 py-4">
                    <Link href={adminPath} className={cn('inline-flex min-h-11 items-center font-display text-xl uppercase', focusRing)}>
                        Admin
                    </Link>
                </div>
                <nav aria-label="Navigation de l'admin" className="px-2 pb-3">
                    <ul className="grid grid-cols-2 gap-1 sm:grid-cols-3 lg:flex lg:flex-col">
                        {links.map((l) => (
                            <li key={l.href}>
                                <Link
                                    href={l.href}
                                    aria-current={active(l) ? 'page' : undefined}
                                    className={cn(
                                        'flex min-h-11 items-center gap-2 whitespace-nowrap px-3 text-sm font-bold lg:gap-3',
                                        active(l) ? 'bg-ink text-paper' : 'hover:bg-paper',
                                        focusRing,
                                    )}
                                >
                                    <l.Icon size={18} aria-hidden="true" />
                                    {l.label}
                                </Link>
                            </li>
                        ))}
                        <li className="lg:mt-4">
                            <a
                                href="/"
                                target="_blank"
                                rel="noopener noreferrer"
                                className={cn('flex min-h-11 items-center gap-2 whitespace-nowrap px-3 text-sm font-bold hover:bg-paper lg:gap-3', focusRing)}
                            >
                                <IconExternalLink size={18} aria-hidden="true" />
                                Voir le site
                                <span className="sr-only">(nouvel onglet)</span>
                            </a>
                        </li>
                        <li>
                            <button
                                type="button"
                                onClick={() => router.post(`${adminPath}/deconnexion`)}
                                className={cn('flex min-h-11 w-full items-center gap-2 whitespace-nowrap px-3 text-sm font-bold hover:bg-paper lg:gap-3', focusRing)}
                            >
                                <IconLogout size={18} aria-hidden="true" />
                                Déconnexion
                            </button>
                        </li>
                    </ul>
                </nav>
            </aside>
            <main className="min-w-0 px-4 py-8 sm:px-8">
                <h1 className="font-display text-3xl uppercase md:text-4xl">{title}</h1>
                {flash?.success && (
                    <p role="status" className="mt-6 border-2 border-success bg-surface p-3 text-sm font-bold text-success">
                        {flash.success}
                    </p>
                )}
                <div className="mt-8">{children}</div>
            </main>
        </div>
    );
}

/** Admin toujours en sombre ; en quittant l'admin, retour au choix du visiteur (jour par défaut). */
export function useAdminDarkMode() {
    useEffect(() => {
        document.documentElement.classList.add('dark');
        return () => {
            let saved = null;
            try {
                saved = localStorage.getItem('theme');
            } catch (e) {
                // stockage indisponible : jour par défaut
            }
            document.documentElement.classList.toggle('dark', saved === 'dark');
        };
    }, []);
}
