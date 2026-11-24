import { Head } from '@inertiajs/react';
import CategoryFilter from '@/Components/CategoryFilter';
import ProjectCard from '@/Components/ProjectCard';
import SectionTitle from '@/Components/SectionTitle';
import AppLayout from '@/Layouts/AppLayout';
import useSideReveal from '@/lib/useSideReveal';
import useTrans from '@/lib/useTrans';

const rotations = ['-rotate-3', 'rotate-2', 'rotate-[4deg]', '-rotate-2', 'rotate-3', '-rotate-[4deg]'];

export default function Index({ projects, categories, activeCategory, total }) {
    const { t } = useTrans();
    const { ref: gridRef, itemProps } = useSideReveal();

    return (
        <AppLayout>
            <Head title={t('Projets')} />
            <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <SectionTitle as="h1" title={t('Projets')} separator subtitle={total > 0 ? t(total > 1 ? ':count projets publiés' : ':count projet publié', { count: total }) : null} />

                {categories.length > 1 && (
                    <div className="mt-10">
                        <CategoryFilter categories={categories} active={activeCategory} count={projects.length} />
                    </div>
                )}

                {projects.length > 0 ? (
                    <div ref={gridRef} className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                        {projects.map((project, i) => (
                            <ProjectCard key={project.id} project={project} index={i + 1} rotate={rotations[i % rotations.length]} {...itemProps(i)} />
                        ))}
                    </div>
                ) : (
                    <p className="mt-12 border-2 border-ink bg-surface p-8 text-lg">{t('Aucun projet publié pour le moment.')}</p>
                )}
            </section>
        </AppLayout>
    );
}
