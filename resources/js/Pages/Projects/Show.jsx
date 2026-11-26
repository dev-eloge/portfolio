import { Link } from '@inertiajs/react';
import { IconArrowLeft } from '@tabler/icons-react';
import Button from '@/Components/Button';
import Tag from '@/Components/Tag';
import AppLayout from '@/Layouts/AppLayout';
import useTrans from '@/lib/useTrans';
import PageTitle from '@/Components/PageTitle';

const steps = [
    { key: 'context', title: 'Contexte' },
    { key: 'problem', title: 'Problème' },
    { key: 'solution', title: 'Solution' },
    { key: 'result', title: 'Résultat' },
];

export default function Show({ project }) {
    const caseStudy = steps.filter((s) => project[s.key]);
    const { t } = useTrans();

    return (
        <AppLayout>
            <PageTitle title={project.title} />
            <article className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <Link
                    href="/projets"
                    className="inline-flex min-h-11 items-center gap-2 font-bold focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
                >
                    <IconArrowLeft size={20} aria-hidden="true" />
                    {t('Tous les projets')}
                </Link>

                <p className="mt-8 text-xs font-bold uppercase tracking-wide text-muted md:text-sm">{project.category}</p>
                <h1 className="mt-2 font-display text-[clamp(2.5rem,8vw,6rem)] uppercase leading-[0.9]">{project.title}</h1>
                <p className="mt-6 max-w-2xl text-lg leading-relaxed">{project.description}</p>

                {(project.url_live || project.url_code) && (
                    <div className="mt-8 flex flex-wrap gap-4">
                        {project.url_live && (
                            <Button href={project.url_live} external>
                                {t('Voir le site')}
                            </Button>
                        )}
                        {project.url_code && (
                            <Button variant="secondary" href={project.url_code} external>
                                {t('Code source')}
                            </Button>
                        )}
                    </div>
                )}

                {project.image && (
                    <figure className="mt-14 -rotate-1 bg-surface p-3 pb-8 shadow-[0_1px_0_rgba(0,0,0,.08),0_10px_20px_-10px_rgba(0,0,0,.35)] motion-reduce:rotate-0 md:p-4 md:pb-10">
                        <img src={project.image} alt={project.image_alt ?? ''} width="1200" height="900" className="w-full border border-ink object-cover" />
                    </figure>
                )}

                <div className="mt-16 grid gap-12 md:grid-cols-[1fr_16rem]">
                    <div className="space-y-10">
                        {caseStudy.map((s) => (
                            <section key={s.key}>
                                <h2 className="font-display text-2xl uppercase md:text-3xl">{t(s.title)}</h2>
                                <p className="mt-3 whitespace-pre-line leading-relaxed">{project[s.key]}</p>
                            </section>
                        ))}
                    </div>

                    {project.technologies?.length > 0 && (
                        <aside>
                            <h2 className="font-display text-xl uppercase">{t('Stack')}</h2>
                            <ul className="mt-4 flex flex-wrap gap-2">
                                {project.technologies.map((tech) => (
                                    <li key={tech}>
                                        <Tag>{tech}</Tag>
                                    </li>
                                ))}
                            </ul>
                        </aside>
                    )}
                </div>
            </article>
        </AppLayout>
    );
}
