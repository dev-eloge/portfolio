import cn from '@/lib/cn';

/**
 * Portrait du hero : visage recadré en rond, double contour net
 * (anneau ink + anneau accent) et traits « néon » de part et d'autre.
 */
function Rays({ className }) {
    return (
        <svg viewBox="0 0 120 90" aria-hidden="true" className={cn('absolute stroke-accent', className)} fill="none" strokeWidth="5" strokeLinecap="round">
            <path d="M8 18 L62 46" />
            <path d="M40 6 L76 40" />
            <path d="M4 52 L58 58" />
        </svg>
    );
}

export default function HeroPortrait({ src, alt }) {
    return (
        <div className="relative size-60 sm:size-72 md:size-80">
            <Rays className="-left-28 top-[8%] hidden w-28 sm:block md:-left-36 md:w-36" />
            <Rays className="-right-24 top-[18%] hidden w-24 -scale-x-100 sm:block md:-right-32 md:w-32" />

            <div className="size-full rounded-full bg-ink p-1">
                <div className="size-full rounded-full bg-accent p-2 md:p-2.5">
                    <img src={src} alt={alt} width="360" height="360" className="size-full rounded-full object-cover" />
                </div>
            </div>
        </div>
    );
}
