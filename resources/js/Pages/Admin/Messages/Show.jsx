import { Link, router, usePage } from '@inertiajs/react';
import { IconArrowLeft, IconMail, IconTrash } from '@tabler/icons-react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Show({ message }) {
    const { adminPath } = usePage().props;

    const remove = () => {
        if (window.confirm('Supprimer ce message ? Cette action est définitive.')) {
            router.delete(`${adminPath}/messages/${message.id}`);
        }
    };

    return (
        <AdminLayout title={message.subject || 'Message'}>
            <Link href={`${adminPath}/messages`} className="inline-flex min-h-11 items-center gap-2 font-bold">
                <IconArrowLeft size={18} aria-hidden="true" /> Tous les messages
            </Link>
            <article className="mt-4 max-w-3xl border-2 border-ink bg-surface p-6">
                <p className="font-bold">{message.name}</p>
                <p className="text-muted">{message.email} · <span className="font-mono text-sm">{message.date}</span></p>
                <p className="mt-6 whitespace-pre-line leading-relaxed">{message.message}</p>
                <div className="mt-8 flex flex-wrap gap-3">
                    <a href={`mailto:${message.email}?subject=${encodeURIComponent('Re: ' + (message.subject || 'votre message'))}`} className="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 font-bold text-on-accent">
                        <IconMail size={18} aria-hidden="true" /> Répondre
                    </a>
                    <button type="button" onClick={remove} className="inline-flex min-h-11 items-center gap-2 rounded-full border-2 border-ink px-5 font-bold text-danger">
                        <IconTrash size={18} aria-hidden="true" /> Supprimer
                    </button>
                </div>
            </article>
        </AdminLayout>
    );
}
