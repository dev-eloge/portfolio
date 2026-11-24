import { Link, router } from '@inertiajs/react';
import { IconPencil, IconPlus, IconTrash } from '@tabler/icons-react';
import AdminLayout from '@/Layouts/AdminLayout';

const btn = 'inline-flex min-h-11 items-center gap-2 font-bold focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent';

export default function Index({ title, singular, route, columns, rows }) {
    const base = route ? window.location.pathname.replace(/\/$/, '') : '';

    const remove = (row) => {
        if (window.confirm(`Supprimer « ${row.cells[0]} » ? Cette action est définitive.`)) {
            router.delete(`${base}/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <AdminLayout title={title}>
            <Link href={`${base}/create`} className={`${btn} rounded-full bg-accent px-5 text-on-accent`}>
                <IconPlus size={18} aria-hidden="true" />
                Ajouter : {singular.toLowerCase()}
            </Link>

            {rows.length ? (
                <div className="mt-6 overflow-x-auto border-2 border-ink bg-surface">
                    <table className="w-full text-left text-sm">
                        <thead className="border-b-2 border-ink">
                            <tr>
                                {columns.map((c) => (
                                    <th key={c} scope="col" className="px-4 py-3 font-bold uppercase">{c}</th>
                                ))}
                                <th scope="col" className="px-4 py-3 text-right font-bold uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-ink/20">
                            {rows.map((row) => (
                                <tr key={row.id}>
                                    {row.cells.map((cell, i) => (
                                        <td key={i} className="px-4 py-2">{cell}</td>
                                    ))}
                                    <td className="whitespace-nowrap px-4 py-2 text-right">
                                        <Link href={`${base}/${row.id}/edit`} className={`${btn} px-2`} aria-label={`Modifier ${row.cells[0]}`}>
                                            <IconPencil size={18} aria-hidden="true" />
                                        </Link>
                                        <button type="button" onClick={() => remove(row)} className={`${btn} px-2 text-danger`} aria-label={`Supprimer ${row.cells[0]}`}>
                                            <IconTrash size={18} aria-hidden="true" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            ) : (
                <p className="mt-6 border-2 border-ink bg-surface p-6">Rien pour le moment.</p>
            )}
        </AdminLayout>
    );
}
