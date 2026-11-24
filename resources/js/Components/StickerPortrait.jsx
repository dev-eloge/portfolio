import cn from '@/lib/cn';

/**
 * Portrait « sticker » : photo non détourée, encadrée d'un contour accent épais,
 * légèrement inclinée comme un autocollant posé sur la bande noire.
 */
export default function StickerPortrait({ src, alt, className = '' }) {
    return (
        <figure className={cn('relative z-10 w-56 -rotate-2 motion-reduce:rotate-0 md:w-80', className)}>
            <img
                src={src}
                alt={alt}
                width="567"
                height="1008"
                loading="lazy"
                className="aspect-[567/1008] w-full border-[10px] border-accent bg-surface object-cover"
            />
        </figure>
    );
}
