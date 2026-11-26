import { IconBrain, IconCode, IconLayout, IconServer, IconTool } from '@tabler/icons-react';
import SectionTitle from '@/Components/SectionTitle';
import Tag from '@/Components/Tag';
import AppLayout from '@/Layouts/AppLayout';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';
import PageTitle from '@/Components/PageTitle';

const icons = { Backend: IconServer, Frontend: IconLayout, IA: IconBrain, Outils: IconTool };
const rotations = ['-rotate-1', 'rotate-1', 'rotate-[0.5deg]', '-rotate-[1.5deg]'];

export default function Skills({ skillGroups }) {
    const { t } = useTrans();

    return (
        <AppLayout>
            <PageTitle title={t('Compétences')} />
            <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <SectionTitle as="h1" title={t('Compétences')} separator />

                {skillGroups.length > 0 ? (
                    <div className="mt-12 grid gap-6 sm:grid-cols-2 md:gap-10">
                        {skillGroups.map((group, i) => {
                            const Icon = icons[group.key] ?? IconCode;
                            return (
                                <section
                                    key={group.key}
                                    className={cn('border-2 border-ink bg-surface p-6 motion-reduce:rotate-0 md:p-8', rotations[i % rotations.length])}
                                >
                                    <div className="flex items-center gap-3">
                                        <Icon size={32} aria-hidden="true" />
                                        <h2 className="font-display text-2xl uppercase">{group.category}</h2>
                                    </div>
                                    <ul className="mt-6 flex flex-wrap gap-2">
                                        {group.skills.map((skill) => (
                                            <li key={skill}>
                                                <Tag>{skill}</Tag>
                                            </li>
                                        ))}
                                    </ul>
                                </section>
                            );
                        })}
                    </div>
                ) : (
                    <p className="mt-12 border-2 border-ink bg-surface p-8 text-lg">{t('Aucune compétence renseignée pour le moment.')}</p>
                )}
            </section>
        </AppLayout>
    );
}
