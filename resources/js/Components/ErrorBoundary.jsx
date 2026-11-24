import { router } from '@inertiajs/react';
import { Component } from 'react';

const TEXTS = {
    fr: { message: "Cette page n'a pas pu s'afficher.", back: "Retour à l'accueil" },
    en: { message: 'This page could not be displayed.', back: 'Back to home' },
};

/**
 * Filet de sécurité : si une page plante, seule cette page affiche un message,
 * et l'application se rétablit à la navigation suivante (au lieu de rester blanche).
 */
export default class ErrorBoundary extends Component {
    state = { error: null };

    static getDerivedStateFromError(error) {
        return { error };
    }

    componentDidCatch(error, info) {
        console.error(error, info.componentStack);
    }

    componentDidMount() {
        this.removeListener = router.on('navigate', () => {
            if (this.state.error) this.setState({ error: null });
        });
    }

    componentWillUnmount() {
        this.removeListener?.();
    }

    render() {
        if (!this.state.error) return this.props.children;

        const t = TEXTS[document.documentElement.lang?.slice(0, 2)] ?? TEXTS.fr;
        return (
            <main className="mx-auto flex min-h-screen max-w-xl flex-col items-center justify-center gap-6 px-4 text-center">
                <p className="font-display text-3xl uppercase">{t.message}</p>
                <a
                    href="/"
                    className="inline-flex min-h-11 items-center rounded-full bg-accent px-5 font-bold text-on-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
                >
                    {t.back}
                </a>
            </main>
        );
    }
}
