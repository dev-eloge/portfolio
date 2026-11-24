import Button from '@/Components/Button';
import ProjectCard from '@/Components/ProjectCard';
import useSideReveal from '@/lib/useSideReveal';
import SectionTitle from '@/Components/SectionTitle';
import useTrans from '@/lib/useTrans';

const rotations = ['-rotate-3', 'rotate-2', 'rotate-[4deg]'];

export default function FeaturedProjects({ projects }) {
    const { t } = useTrans();
    const { ref: gridRef, itemProps } = useSideReveal();

    if (!projects?.length) return null;

    return (
        <section className="mx-auto max-w-6xl px-4 py-20 sm:px-6 md:py-28 lg:px-8">
            <SectionTitle title={t('Projets')} separator />
            <div ref={gridRef} className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                {projects.map((project, i) => (
                    <ProjectCard key={project.id} project={project} index={i + 1} rotate={rotations[i % rotations.length]} {...itemProps(i)} />
                ))}
            </div>
            <div className="mt-14">
                <Button variant="secondary" href="/projets">
                    {t('Tous les projets')}
                </Button>
            </div>
        </section>
    );
}
