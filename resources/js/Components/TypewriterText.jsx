import { useEffect, useRef, useState } from 'react';
import cn from '@/lib/cn';

const CURSOR_BLINK_AFTER_END = 4000; // ms avant que le curseur arrête de clignoter

function prefersReducedMotion() {
    return typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/**
 * Effet terminal : le texte s'écrit caractère par caractère, suivi d'un curseur bloc.
 * Démarre une seule fois quand le texte entre à l'écran.
 */
export default function TypewriterText({ text, as: Tag = 'p', className = '', speed = 25 }) {
    const ref = useRef(null);
    const [reduced] = useState(prefersReducedMotion);
    const [count, setCount] = useState(() => (reduced ? text?.length ?? 0 : 0));
    const [started, setStarted] = useState(reduced);
    const [blinking, setBlinking] = useState(!reduced);

    // Démarrage à l'entrée dans l'écran, une seule fois
    useEffect(() => {
        if (started || !ref.current) return;
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setStarted(true);
                    observer.disconnect();
                }
            },
            { threshold: 0.4 },
        );
        observer.observe(ref.current);
        return () => observer.disconnect();
    }, [started]);

    // Frappe caractère par caractère. La position est calculée à partir du temps écoulé,
    // pas du nombre de minuteries : si le navigateur ralentit la page (onglet en arrière-plan,
    // économie d'énergie), la frappe rattrape son retard au lieu de ralentir ou de se figer.
    useEffect(() => {
        if (!started || reduced || !text) return;
        const start = performance.now();
        let interval;
        const update = () => {
            const next = Math.min(text.length, Math.floor((performance.now() - start) / speed) + 1);
            setCount((c) => (c === next ? c : next));
            if (next >= text.length) clearInterval(interval);
        };
        interval = setInterval(update, speed);
        // Retour sur l'onglet : on se remet tout de suite à la bonne position
        document.addEventListener('visibilitychange', update);
        update();
        return () => {
            clearInterval(interval);
            document.removeEventListener('visibilitychange', update);
        };
    }, [started, reduced, text, speed]);

    // Le curseur arrête de clignoter quelques secondes après la fin
    const done = !!text && count >= text.length;
    useEffect(() => {
        if (!done || reduced) return;
        const timer = setTimeout(() => setBlinking(false), CURSOR_BLINK_AFTER_END);
        return () => clearTimeout(timer);
    }, [done, reduced]);

    if (!text) return null;

    const cursor = (
        <span
            className={cn('ml-0.5 inline-block h-[1.1em] w-[0.55em] translate-y-[0.2em] bg-current', blinking && 'animate-blink motion-reduce:animate-none')}
        />
    );

    return (
        <Tag ref={ref} className={cn('font-mono break-words', className)}>
            <span className="sr-only">{text}</span>
            {/* Copie invisible : réserve la hauteur finale (pas de saut de mise en page) */}
            <span aria-hidden="true" className="grid">
                <span translate="no" className="notranslate invisible col-start-1 row-start-1">
                    {text}
                    {cursor}
                </span>
                {/* Pendant la frappe : « ne pas traduire ». La traduction automatique du navigateur
                    remplace les nœuds texte, ce qui figerait l'animation. Une fois le texte complet,
                    un nouveau nœud (key) est créé, que le navigateur peut alors traduire. */}
                <span
                    key={done ? 'final' : 'typing'}
                    translate={done ? undefined : 'no'}
                    className={cn('col-start-1 row-start-1', !done && 'notranslate')}
                >
                    {text.slice(0, count)}
                    {cursor}
                </span>
            </span>
        </Tag>
    );
}
