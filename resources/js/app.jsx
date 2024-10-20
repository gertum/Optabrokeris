import './bootstrap';
import '../css/app.css';

import {createRoot} from 'react-dom/client';
import {createInertiaApp} from '@inertiajs/react';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {I18nextProvider} from 'react-i18next';
import i18n from 'i18next';
import {StyleProvider} from '@ant-design/cssinjs';
import enTranslations from '../translations/en/translation.json';
import ltTranslations from '../translations/lt/translation.json';
import {ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {NotificationProvider} from '@/Providers/NotificationProvider.jsx';
import {ConfirmationProvider} from "@/Providers/ConfirmationProvider.jsx";

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
    title: title => `${title} - ${appName}`,
    resolve: name =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx')
        ),
    setup({el, App, props}) {
        const root = createRoot(el);

        root.render(
            <I18nextProvider i18n={i18n}>
                <StyleProvider hashPriority="high">
                    <NotificationProvider>
                        <ConfirmationProvider>
                            <App {...props} />
                            <ToastContainer />
                        </ConfirmationProvider>
                    </NotificationProvider>
                </StyleProvider>
            </I18nextProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});
