import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import ErrorBoundary from '@/Components/ErrorBoundary';

const appName = import.meta.env.VITE_APP_NAME || 'Portfolio';

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),
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
