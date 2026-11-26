import { usePage } from '@inertiajs/react';
import Button from '@/Components/Button';
import DevToolsFrame from '@/Components/DevToolsFrame';
import Sparkles from '@/Components/Sparkles';
import Tag from '@/Components/Tag';
import Timeline from '@/Components/Timeline';
import AppLayout from '@/Layouts/AppLayout';
import useTrans from '@/lib/useTrans';
import PageTitle from '@/Components/PageTitle';

const colTitle = 'font-display text-3xl uppercase leading-[0.9] md:text-4xl';

export default function About({ skillGroups, education, experiences }) {
    const { profile } = usePage().props;
    const skills = skillGroups.flatMap((g) => g.skills);
    const { t } = useTrans();

    return (
        <AppLayout>
            <PageTitle title={t('À propos')} />
            <div className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <h1 className="sr-only">{t('À propos')}</h1>
                <Sparkles />

                <div className="mt-12 grid gap-12 md:grid-cols-[auto_1fr] lg:grid-cols-[auto_1fr_1fr] lg:gap-14">
                    {/* Colonne photo */}
                    <div className="mx-auto w-full max-w-[280px] md:mx-0 md:w-64">
                        <DevToolsFrame label="img.portrait">
                            <img
                                src={profile.images.about}
                                alt={profile.images.about_alt}
                                width="567"
                                height="1008"
                                className="portrait aspect-[567/1008] w-full object-cover"
                            />
                        </DevToolsFrame>
                    </div>

                    {/* Colonne Hello + Formation */}
                    <div className="space-y-12">
                        <section>
                            <h2 className="font-display text-5xl uppercase leading-[0.9] md:text-7xl">{t('Hello')}</h2>
                            <p className="mt-4 font-bold">{t('Je suis :name, :role.', { name: profile.name, role: profile.role.charAt(0).toLowerCase() + profile.role.slice(1) })}</p>
                            <div className="mt-4 space-y-4 leading-relaxed">
                                {profile.about?.map((paragraph) => <p key={paragraph}>{paragraph}</p>)}
                            </div>
                        </section>

                        {education.length > 0 && (
                            <section>
                                <h2 className={colTitle}>{t('Formation')}</h2>
                                <ul className="mt-4 space-y-4">
                                    {education.map((e) => (
                                        <li key={e.id}>
                                            <p className="font-bold">{e.title}</p>
                                            <p className="text-muted">
                                                {e.company && <>{e.company} </>}
                                                <span className="font-mono text-sm">{e.period}</span>
                                            </p>
                                        </li>
                                    ))}
                                </ul>
                            </section>
                        )}
                    </div>

                    {/* Colonne Compétences + Expérience */}
                    <div className="space-y-12 md:col-span-2 lg:col-span-1">
                        {skills.length > 0 && (
                            <section>
                                <h2 className={colTitle}>{t('Compétences')}</h2>
                                <ul className="mt-4 flex flex-wrap gap-2">
                                    {skills.map((s) => (
                                        <li key={s}>
                                            <Tag>{s}</Tag>
                                        </li>
                                    ))}
                                </ul>
                                <Button variant="link" href="/competences" className="mt-4">
                                    {t('Toutes les compétences')}
                                </Button>
                            </section>
                        )}

                        {experiences.length > 0 && (
                            <section>
                                <h2 className={colTitle}>{t('Expérience')}</h2>
                                <div className="mt-6">
                                    <Timeline experiences={experiences} compact />
                                </div>
                                <Button variant="link" href="/experience" className="mt-6">
                                    {t('Tout le parcours')}
                                </Button>
                            </section>
                        )}
                    </div>
                </div>

                <div className="mt-16 flex flex-wrap items-center gap-4 border-t-2 border-ink pt-10">
                    <Button href="/contact">{t('Me contacter')}</Button>
                    <Button variant="secondary" href="/projets">
                        {t('Voir mes projets')}
                    </Button>
                    {profile.cv && (
                        <Button variant="link" href={profile.cv} download={`CV-${profile.name.replace(/\s+/g, '-')}.pdf`}>
                            {t('Télécharger le CV')}
                        </Button>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
