import { IconApi, IconBrain, IconCode, IconDatabase, IconDeviceMobile, IconLayout } from '@tabler/icons-react';
import SectionTitle from '@/Components/SectionTitle';
import cn from '@/lib/cn';
import useSideReveal from '@/lib/useSideReveal';
import useTrans from '@/lib/useTrans';

const icons = {
    code: IconCode,
    brain: IconBrain,
    layout: IconLayout,
    database: IconDatabase,
    api: IconApi,
    'device-mobile': IconDeviceMobile,
};

const rotations = ['-rotate-1', 'rotate-1', 'rotate-[0.5deg]', '-rotate-[1.5deg]'];

export default function Services({ services }) {
    const { t } = useTrans();
    const { ref: listRef, itemProps } = useSideReveal();

    if (!services?.length) return null;

    return (
        <section className="mx-auto max-w-6xl px-4 py-20 sm:px-6 md:py-28 lg:px-8">
            <SectionTitle title={t('Ce que je peux construire avec vous')} separator />
            <ul ref={listRef} className="mt-12 grid gap-6 sm:grid-cols-2 md:gap-10">
                {services.map((service, i) => {
                    const Icon = icons[service.icon] ?? IconCode;
                    const reveal = itemProps(i);
                    return (
                        <li
                            key={service.title}
                            // Arrivée depuis son côté, puis survol : la carte se redresse et monte
                            className={cn(
                                'group border-2 border-ink bg-surface p-6 md:p-8',
                                reveal.className,
                                'hover:rotate-0 hover:-translate-y-1',
                                'max-sm:rotate-0 motion-reduce:rotate-0 motion-reduce:hover:translate-y-0',
                                rotations[i % rotations.length],
                            )}
                        >
                            <Icon size={32} aria-hidden="true" className="transition-colors duration-200 group-hover:text-accent-ink" />
                            <h3 className="mt-4 font-display text-xl uppercase md:text-2xl">{service.title}</h3>
                            <p className="mt-3 leading-relaxed text-muted">{service.description}</p>
                        </li>
                    );
                })}
            </ul>
        </section>
    );
}
