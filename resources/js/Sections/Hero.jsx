import { Link, usePage } from '@inertiajs/react';
import { IconChevronRight, IconCode } from '@tabler/icons-react';
import Button from '@/Components/Button';
import HeroPortrait from '@/Components/HeroPortrait';
import TypewriterText from '@/Components/TypewriterText';
import useTrans from '@/lib/useTrans';

/** Hero repris de l'architecture de la maquette Brutalist (inspiration 2). */
export default function Hero({ highlights }) {
    const { profile } = usePage().props;
    const { t } = useTrans();

    const values = [
        highlights.projects.count > 0 && {
            title: t(highlights.projects.count > 1 ? ':count projets' : ':count projet', { count: highlights.projects.count }),
            detail: highlights.projects.detail.join(', '),
        },
        highlights.skills.count > 0 && {
            title: t(highlights.skills.count > 1 ? ':count compétences' : ':count compétence', { count: highlights.skills.count }),
            detail: highlights.skills.detail.join(', '),
        },
        highlights.experiences.count > 0 && {
            title: t(highlights.experiences.count > 1 ? ':count expériences' : ':count expérience', { count: highlights.experiences.count }),
            detail: highlights.experiences.since ? t('En activité depuis :year', { year: highlights.experiences.since }) : null,
        },
    ].filter(Boolean);

    return (
        <section className="mx-auto max-w-6xl px-4 pb-6 pt-12 text-center sm:px-6 md:pb-10 md:pt-16 lg:px-8">
            {/* Badge pilule */}
            <Link
                href="/a-propos"
                className="group inline-flex min-h-11 items-center gap-2 rounded-full border-2 border-ink bg-surface py-1 pl-1 pr-3 text-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
            >
                <span aria-hidden="true" className="grid h-7 w-9 place-items-center rounded-full bg-accent text-on-accent">
                    <IconCode size={18} stroke={2.25} />
                </span>
                <span className="font-medium">{profile.role}</span>
                <IconChevronRight size={16} aria-hidden="true" className="transition-transform group-hover:translate-x-0.5 motion-reduce:transform-none" />
            </Link>

            {/* Titre géant */}
            <h1 className="mx-auto mt-6 max-w-5xl font-display text-[clamp(2.6rem,9vw,7rem)] uppercase leading-[0.95] tracking-[-0.01em]">
                {t('Bonjour, je suis :name', { name: profile.name })}
            </h1>

            {/* Intro en effet terminal (texte = profile.intro) */}
            <TypewriterText text={profile.intro} className="mx-auto mt-6 max-w-2xl text-sm leading-relaxed text-muted md:text-base" />

            {/* Boutons pilule */}
            <div className="mt-8 flex flex-wrap items-center justify-center gap-4">
                <Button href="/projets">{t('Voir mes projets')}</Button>
                <Button variant="secondary" href="/contact">
                    {t('Me contacter')}
                </Button>
                {profile.cv && (
                    <Button variant="link" href={profile.cv} download>
                        {t('Télécharger le CV')}
                    </Button>
                )}
            </div>

            {/* Portrait : visage recadré en rond */}
            <div className="mt-14 flex justify-center md:mt-16">
                <HeroPortrait src={profile.images.face} alt={profile.images.face_alt} />
            </div>

            {/* Rangée de 3 valeurs séparées par des filets verticaux */}
            {values.length > 0 && (
                <dl className="mt-14 grid divide-y-2 divide-ink md:mt-16 md:grid-cols-3 md:divide-x-2 md:divide-y-0">
                    {values.map((v) => (
                        <div key={v.title} className="flex flex-col items-center px-6 py-6 md:py-2">
                            <dt className="font-display text-3xl uppercase md:text-4xl">{v.title}</dt>
                            {v.detail && <dd className="mt-3 max-w-xs text-sm leading-relaxed text-muted">{v.detail}</dd>}
                        </div>
                    ))}
                </dl>
            )}
        </section>
    );
}
