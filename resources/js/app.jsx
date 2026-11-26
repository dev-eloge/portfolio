import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import ErrorBoundary from '@/Components/ErrorBoundary';

createInertiaApp({
    // Chaque page fournit son titre complet (composant PageTitle, ou « … — Admin » dans l'admin)
    title: (title) => title,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <ErrorBoundary>
                <App {...props} />
            </ErrorBoundary>,
        );
    },
    progress: {
        color: 'var(--color-accent)',
    },
});
