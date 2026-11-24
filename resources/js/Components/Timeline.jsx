import Badge from '@/Components/Badge';
import useTrans from '@/lib/useTrans';

export default function Timeline({ experiences, compact = false }) {
    const { t } = useTrans();

    return (
        <ol className="relative ml-2 border-l-2 border-ink">
            {experiences.map((xp) => (
                <li key={xp.id} className={compact ? 'relative pb-8 pl-6 last:pb-0' : 'relative pb-12 pl-8 last:pb-0 md:pl-10'}>
                    <span aria-hidden="true" className="absolute -left-[9px] top-1 size-4 rounded-full border-2 border-ink bg-accent" />
                    <p className="flex flex-wrap items-center gap-2 font-mono text-sm">
                        {xp.period}
                        {/* Badge réservé aux formations en cours ; pour un emploi, « 2026 – Aujourd'hui » suffit */}
                        {xp.current && xp.kind === 'education' && <Badge featured>{t('En cours')}</Badge>}
                    </p>
                    <h3 className={compact ? 'mt-1 font-bold' : 'mt-2 font-display text-xl uppercase md:text-2xl'}>{xp.title}</h3>
                    {(xp.company || xp.type) && (
                        <p className="text-muted">{[xp.company, xp.type].filter(Boolean).join(' · ')}</p>
                    )}
                    {!compact && xp.description && <p className="mt-3 max-w-2xl leading-relaxed">{xp.description}</p>}
                </li>
            ))}
        </ol>
    );
}
