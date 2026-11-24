import { Head } from '@inertiajs/react';
import { IconCertificate } from '@tabler/icons-react';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import SectionTitle from '@/Components/SectionTitle';
import Tag from '@/Components/Tag';
import AppLayout from '@/Layouts/AppLayout';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const rotations = ['-rotate-1', 'rotate-1', 'rotate-[0.5deg]', '-rotate-[1.5deg]'];

export default function Certifications({ certifications }) {
    const { t } = useTrans();

    return (
        <AppLayout>
            <Head title={t('Certifications')} />
            <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <SectionTitle as="h1" title={t('Certifications')} separator />

                {certifications.length > 0 ? (
                    <ul className="mt-12 grid gap-6 sm:grid-cols-2 md:gap-10">
                        {certifications.map((c, i) => (
                            <li
                                key={c.id}
                                className={cn(
                                    'flex flex-col border-2 border-ink bg-surface p-6 motion-reduce:rotate-0 md:p-8',
                                    rotations[i % rotations.length],
                                )}
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <IconCertificate size={36} aria-hidden="true" className="shrink-0" />
                                    {c.expired && <Badge>{t('Expirée')}</Badge>}
                                </div>

                                <p className="mt-4 text-xs font-bold uppercase tracking-wide text-muted">{c.issuer}</p>
                                <h2 className="mt-1 font-display text-xl uppercase md:text-2xl">{c.title}</h2>

                                <dl className="mt-4 space-y-1 font-mono text-sm">
                                    <div className="flex gap-2">
                                        <dt className="text-muted">{t('Obtenue :')}</dt>
                                        <dd>{c.issued}</dd>
                                    </div>
                                    {c.expires && (
                                        <div className="flex gap-2">
                                            <dt className="text-muted">{c.expired ? t('Expirée :') : t('Valable jusqu’à :')}</dt>
                                            <dd>{c.expires}</dd>
                                        </div>
                                    )}
                                    {c.credential_id && (
                                        <div className="flex gap-2">
                                            <dt className="text-muted">{t('Identifiant :')}</dt>
                                            <dd className="break-all">{c.credential_id}</dd>
                                        </div>
                                    )}
                                </dl>

                                {c.skills.length > 0 && (
                                    <ul className="mt-5 flex flex-wrap gap-2">
                                        {c.skills.map((skill) => (
                                            <li key={skill}>
                                                <Tag>{skill}</Tag>
                                            </li>
                                        ))}
                                    </ul>
                                )}

                                {c.credential_url && (
                                    <div className="mt-auto pt-6">
                                        <Button variant="link" href={c.credential_url} external arrow={false}>
                                            {t('Voir le certificat')}
                                        </Button>
                                    </div>
                                )}
                            </li>
                        ))}
                    </ul>
                ) : (
                    <p className="mt-12 border-2 border-ink bg-surface p-8 text-lg">{t('Aucune certification renseignée pour le moment.')}</p>
                )}
            </section>
        </AppLayout>
    );
}
