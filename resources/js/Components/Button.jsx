import { Link } from '@inertiajs/react';
import { IconArrowRight, IconArrowUpRight } from '@tabler/icons-react';
import cn from '@/lib/cn';

const base =
    'group inline-flex min-h-11 items-center gap-3 whitespace-nowrap rounded-full font-bold transition-colors duration-200 ' +
    'focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent';

const variants = {
    primary: 'bg-accent text-on-accent pl-5 pr-1.5 py-1.5',
    secondary: 'border-2 border-ink text-ink pl-5 pr-1.5 py-1 hover:bg-surface',
    link: 'text-accent-ink underline underline-offset-4 decoration-2 px-0',
};

export default function Button({ variant = 'primary', href, external = false, arrow, className = '', children, ...props }) {
    const showArrow = arrow ?? variant !== 'link';
    const Icon = external ? IconArrowUpRight : IconArrowRight;
    const classes = cn(base, variants[variant], className);

    const content = (
        <>
            <span>{children}</span>
            {showArrow && (
                <span className="grid size-8 place-items-center rounded-full bg-ink text-paper">
                    <Icon size={18} aria-hidden="true" className="transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transform-none" />
                </span>
            )}
        </>
    );

    // Téléchargement (ex. CV) : lien HTML classique. <Link> d'Inertia intercepterait le clic
    // et tenterait d'afficher le fichier comme une page du site.
    if (href && props.download) {
        return (
            <a href={href} className={classes} {...props}>
                {content}
            </a>
        );
    }
    if (href && external) {
        return (
            <a href={href} target="_blank" rel="noopener noreferrer" className={classes} {...props}>
                {content}
            </a>
        );
    }
    if (href) {
        return (
            <Link href={href} className={classes} {...props}>
                {content}
            </Link>
        );
    }
    return (
        <button className={classes} {...props}>
            {content}
        </button>
    );
}
