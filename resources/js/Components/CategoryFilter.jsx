import { Link } from '@inertiajs/react';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const pill =
    'inline-flex min-h-11 items-center border-2 border-ink px-4 py-2 text-sm font-bold uppercase transition-colors ' +
    'focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent';

export default function CategoryFilter({ categories, active, count }) {
    const { t } = useTrans();
    const item = (href, label, isActive) => (
        <li key={href}>
            <Link
                href={href}
                preserveScroll
                aria-current={isActive ? 'page' : undefined}
                className={cn(pill, isActive ? 'bg-ink text-paper' : 'text-ink hover:bg-surface')}
            >
                {label}
            </Link>
        </li>
    );

    return (
        <div className="flex flex-wrap items-center justify-between gap-4">
            <ul className="flex flex-wrap gap-2">
                {item('/projets', t('Tous les projets'), !active)}
                {categories.map((c) => item(`/projets?category=${c.category_slug}`, c.category, active === c.category_slug))}
            </ul>
            <p className="font-mono text-sm" aria-live="polite">
                {t(count > 1 ? ':count projets' : ':count projet', { count })}
            </p>
        </div>
    );
}
