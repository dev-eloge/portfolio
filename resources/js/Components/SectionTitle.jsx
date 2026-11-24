import Sparkles from '@/Components/Sparkles';

export default function SectionTitle({ eyebrow, title, subtitle, separator = false, as: Heading = 'h2', children }) {
    return (
        <div>
            {separator && <Sparkles className="mb-6" />}
            {eyebrow && <p className="text-xs font-bold uppercase tracking-wide text-muted md:text-sm">{eyebrow}</p>}
            <Heading className="mt-2 font-display text-4xl uppercase leading-[0.9] md:text-6xl">{title}</Heading>
            {subtitle && <p className="mt-4 max-w-2xl text-muted">{subtitle}</p>}
            {children}
        </div>
    );
}
