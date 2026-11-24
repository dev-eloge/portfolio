import cn from '@/lib/cn';

// Contour irrégulier de papier déchiré (bord supérieur d'une forme pleine)
const PATH =
    'M0 40 L0 24 L32 17 L58 26 L96 12 L131 21 L162 9 L204 23 L238 15 L271 27 L309 11 L347 19 L380 8 ' +
    'L418 22 L455 14 L487 25 L526 10 L561 20 L600 13 L636 26 L672 9 L710 18 L748 24 L781 11 L819 21 ' +
    'L856 7 L893 19 L929 26 L966 12 L1004 22 L1039 10 L1077 20 L1112 15 L1151 25 L1200 14 L1200 40 Z';

export default function TornEdge({ position = 'top', color = 'fill-band' }) {
    return (
        <svg
            aria-hidden="true"
            viewBox="0 0 1200 40"
            preserveAspectRatio="none"
            className={cn(
                'pointer-events-none absolute left-0 h-6 w-full md:h-10',
                position === 'top' ? 'bottom-[calc(100%-1px)]' : 'top-[calc(100%-1px)] rotate-180',
                color,
            )}
        >
            <path d={PATH} />
        </svg>
    );
}
