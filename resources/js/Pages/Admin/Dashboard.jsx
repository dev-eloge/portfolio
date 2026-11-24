import { Link, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import cn from '@/lib/cn';

export default function Dashboard({ counts, latest }) {
    const { adminPath } = usePage().props;

    return (
        <AdminLayout title="Tableau de bord">
            <ul className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                {counts.map((c) => (
                    <li key={c.label}>
                        <Link href={c.href} className="block border-2 border-ink bg-surface p-5 hover:bg-paper focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                            <p className="font-display text-4xl">{c.value}</p>
                            <p className="mt-1 text-sm font-bold uppercase text-muted">{c.label}</p>
                        </Link>
                    </li>
                ))}
            </ul>

            <h2 className="mt-12 font-display text-2xl uppercase">Derniers messages</h2>
            {latest.length ? (
                <ul className="mt-4 divide-y-2 divide-ink border-2 border-ink bg-surface">
                    {latest.map((m) => (
                        <li key={m.id}>
                            <Link href={`${adminPath}/messages/${m.id}`} className="flex flex-wrap items-center justify-between gap-2 p-4 hover:bg-paper">
                                <span className={cn(!m.read && 'font-bold')}>
                                    {m.name} — {m.subject}
                                </span>
                                <span className="font-mono text-sm text-muted">{m.date}</span>
                            </Link>
                        </li>
                    ))}
                </ul>
            ) : (
                <p className="mt-4 text-muted">Aucun message pour le moment.</p>
            )}
        </AdminLayout>
    );
}
