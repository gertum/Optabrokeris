import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { I18nextProvider } from 'react-i18next';
import i18n from 'i18next';
import enTranslations from '../translations/en/translation.json'; // Import your translation JSON files
import ltTranslations from '../translations/lt/translation.json';

// Load translations and configure i18next
i18n.init({
    lng: 'en',
    resources: {
        en: {
            translation: enTranslations,
        },
        lt: {
            translation: ltTranslations,
        },
    },
    interpolation: {
        escapeValue: false,
    },
});

const appName = import.meta.env.VITE_APP_NAME || 'Inkodus opta broker';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <I18nextProvider i18n={i18n}>
                <App {...props} />
            </I18nextProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});
