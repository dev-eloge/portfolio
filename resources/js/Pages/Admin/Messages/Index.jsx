import { Link, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import cn from '@/lib/cn';

export default function Index({ messages }) {
    const { adminPath } = usePage().props;

    return (
        <AdminLayout title="Messages">
            {messages.length ? (
                <ul className="divide-y-2 divide-ink border-2 border-ink bg-surface">
                    {messages.map((m) => (
                        <li key={m.id}>
                            <Link href={`${adminPath}/messages/${m.id}`} className="block p-4 hover:bg-paper focus-visible:outline-2 focus-visible:outline-accent">
                                <p className="flex flex-wrap items-center justify-between gap-2">
                                    <span className={cn(!m.read && 'font-bold')}>
                                        {!m.read && <span className="mr-2 inline-block size-2 rounded-full bg-accent" aria-label="Non lu" />}
                                        {m.name} — {m.email}
                                    </span>
                                    <span className="font-mono text-sm text-muted">{m.date}</span>
                                </p>
                                <p className="mt-1 text-sm text-muted">{m.subject ? `${m.subject} · ` : ''}{m.excerpt}</p>
                            </Link>
                        </li>
                    ))}
                </ul>
            ) : (
                <p className="border-2 border-ink bg-surface p-6">Aucun message pour le moment.</p>
            )}
        </AdminLayout>
    );
}
