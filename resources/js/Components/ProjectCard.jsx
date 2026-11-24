import { usePage } from '@inertiajs/react';
import { IconBrandGithub, IconExternalLink, IconSparkles } from '@tabler/icons-react';
import { useAssistant } from '@/Components/Assistant/AssistantContext';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import Tag from '@/Components/Tag';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const iconLink =
    'grid size-11 place-items-center rounded-full border-2 border-ink text-ink transition-colors hover:bg-paper ' +
    'focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent';

export default function ProjectCard({ project, rotate = '', index = 1, className = '', style }) {
    const { t } = useTrans();
    const { assistant: assistantEnabled } = usePage().props;
    const assistant = useAssistant();

    return (
        <article
            style={style}
            className={cn(
                'group relative bg-surface p-3 pb-6 shadow-[0_1px_0_rgba(0,0,0,.08),0_10px_20px_-10px_rgba(0,0,0,.35)]',
                'hover:-translate-y-1 hover:rotate-0 max-sm:rotate-0 motion-reduce:rotate-0 motion-reduce:hover:translate-y-0',
                rotate,
                className,
            )}
        >
            <div className="relative">
                {project.image ? (
                    <img
                        src={project.image}
                        alt={project.image_alt ?? ''}
                        loading="lazy"
                        width="800"
                        height="600"
                        className="aspect-[4/3] w-full border border-ink object-cover"
                    />
                ) : (
                    <div className="aspect-[4/3] w-full border border-ink bg-paper" aria-hidden="true" />
                )}
                <Badge featured={project.featured} className="absolute left-2 top-2">
                    {project.featured ? t('À la une') : `#${String(index).padStart(2, '0')}`}
                </Badge>
            </div>

            <div className="mt-4 px-1">
                <p className="text-xs font-bold uppercase tracking-wide text-muted">{project.category}</p>
                <h3 className="mt-1 font-display text-xl uppercase">{project.title}</h3>
                <p className="mt-2 line-clamp-3 text-muted">{project.description}</p>

                {project.technologies?.length > 0 && (
                    <ul className="mt-4 flex flex-wrap gap-2">
                        {project.technologies.map((tech) => (
                            <li key={tech}>
                                <Tag>{tech}</Tag>
                            </li>
                        ))}
                    </ul>
                )}

                <div className="mt-5 flex flex-wrap items-center gap-3">
                    <Button variant="link" href={`/projets/${project.slug}`}>
                        {t("Voir l'étude de cas")}
                    </Button>
                    <div className="ml-auto flex gap-2">
                        {project.url_live && (
                            <a href={project.url_live} target="_blank" rel="noopener noreferrer" aria-label={t('Site live de :title', { title: project.title })} className={iconLink}>
                                <IconExternalLink size={20} aria-hidden="true" />
                            </a>
                        )}
                        {project.url_code && (
                            <a href={project.url_code} target="_blank" rel="noopener noreferrer" aria-label={t('Code source de :title', { title: project.title })} className={iconLink}>
                                <IconBrandGithub size={20} aria-hidden="true" />
                            </a>
                        )}
                    </div>
                </div>

                {assistantEnabled && assistant && (
                    <button
                        type="button"
                        onClick={() => assistant.open({ project: project.slug })}
                        className="mt-4 inline-flex min-h-11 items-center gap-2 text-sm font-bold underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
                    >
                        <IconSparkles size={18} aria-hidden="true" />
                        {t("Expliquer avec l'IA")}
                    </button>
                )}
            </div>
        </article>
    );
}
