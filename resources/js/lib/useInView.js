import { useEffect, useRef, useState } from 'react';

/**
 * Passe à `true` quand l'élément entre à l'écran (une seule fois).
 * Avec prefers-reduced-motion, vaut `true` d'emblée (pas d'animation d'apparition).
 */
export default function useInView({ threshold = 0.2 } = {}) {
    const ref = useRef(null);
    const [inView, setInView] = useState(
        () => typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    );

    useEffect(() => {
        if (inView || !ref.current) return;
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setInView(true);
                    observer.disconnect();
                }
            },
            { threshold },
        );
        observer.observe(ref.current);
        return () => observer.disconnect();
    }, [inView, threshold]);

    return [ref, inView];
}
