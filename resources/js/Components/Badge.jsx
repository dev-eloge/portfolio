import cn from '@/lib/cn';

export default function Badge({ featured = false, children, className = '' }) {
    return (
        <span
            className={cn(
                'inline-block px-2 py-0.5 text-xs',
                featured ? 'bg-accent font-bold uppercase text-on-accent' : 'bg-ink font-mono text-paper',
                className,
            )}
        >
            {children}
        </span>
    );
}
