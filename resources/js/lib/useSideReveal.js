import { useCallback, useLayoutEffect, useState } from 'react';
import useInView from '@/lib/useInView';

// Classes écrites en entier pour que Tailwind les génère
const HIDDEN = {
    left: '-translate-x-24 opacity-0 scale-95',
    right: 'translate-x-24 opacity-0 scale-95',
    center: 'translate-y-16 opacity-0 scale-95',
};
const SHOWN = 'translate-x-0 translate-y-0 opacity-100 scale-100';
const TRANSITION =
    'transition-[opacity,translate,scale,rotate] duration-[1200ms] ease-spring hover:duration-200 hover:ease-out motion-reduce:transition-none';

/**
 * Apparition d'une grille de cartes : toutes en même temps, chacune depuis son côté
 * (colonne de gauche depuis la gauche, de droite depuis la droite, centre ou colonne
 * unique depuis le bas). Le côté est mesuré selon la position réelle à l'écran, donc
 * valable pour 1, 2 ou 3 colonnes. Une seule fois ; prefers-reduced-motion : visible d'emblée.
 *
 *   const { ref, itemProps } = useSideReveal();
 *   <ul ref={ref}>{items.map((x, i) => <li {...itemProps(i)} />)}</ul>
 */
export default function useSideReveal() {
    const [ref, inView] = useInView({ threshold: 0.15 });
    const [sides, setSides] = useState([]);

    const measure = useCallback(() => {
        const grid = ref.current;
        if (!grid) return;
        const middle = grid.offsetWidth / 2;
        setSides(
            [...grid.children].map((child) => {
                // Position dans la grille, sans la translation de départ (offsetLeft ignore les
                // transformations). offsetLeft est relatif à l'offsetParent : la grille si elle est
                // positionnée, sinon le même ancêtre que la grille.
                const left = child.offsetParent === grid ? child.offsetLeft : child.offsetLeft - grid.offsetLeft;
                const gap = left + child.offsetWidth / 2 - middle;
                if (Math.abs(gap) < child.offsetWidth / 4) return 'center';
                return gap < 0 ? 'left' : 'right';
            }),
        );
    }, [ref]);

    // Mesure avant l'affichage, puis à chaque redimensionnement tant que les cartes ne sont pas apparues
    useLayoutEffect(() => {
        if (inView) return;
        measure();
        window.addEventListener('resize', measure);
        return () => window.removeEventListener('resize', measure);
    }, [inView, measure]);

    const itemProps = (i) => ({
        className: `${TRANSITION} ${inView ? SHOWN : HIDDEN[sides[i] ?? 'center']}`,
    });

    return { ref, itemProps };
}
