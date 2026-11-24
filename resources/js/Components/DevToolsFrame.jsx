import cn from '@/lib/cn';

const handle = 'absolute size-2 border border-accent bg-surface';

/**
 * Cadre de sélection façon DevTools (clin d'œil au cadre Figma de la maquette).
 * `label` = vrai sélecteur CSS de l'élément encadré.
 */
export default function DevToolsFrame({ label, className = '', children }) {
    return (
        <div className={cn('relative border border-accent p-2', className)}>
            <span aria-hidden="true" className="absolute -top-6 left-[-1px] bg-accent px-1.5 py-0.5 font-mono text-xs text-on-accent">
                {label}
            </span>
            <span aria-hidden="true" className={cn(handle, '-left-1 -top-1')} />
            <span aria-hidden="true" className={cn(handle, '-right-1 -top-1')} />
            <span aria-hidden="true" className={cn(handle, '-bottom-1 -left-1')} />
            <span aria-hidden="true" className={cn(handle, '-bottom-1 -right-1')} />
            {children}
        </div>
    );
}
