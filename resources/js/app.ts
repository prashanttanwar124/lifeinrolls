import { definePreset } from '@primevue/themes';
import Aura from '@primevue/themes/aura';
import { createInertiaApp } from '@inertiajs/vue3';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import { createApp, h } from 'vue';

import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Life in Rolls';

const LifeInRollsPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '#fff5f2',
            100: '#ffe8e2',
            200: '#ffd3c7',
            300: '#ffb3a1',
            400: '#ff8a6e',
            500: '#FF7253',
            600: '#F05633',
            700: '#D9401F',
            800: '#B82E11',
            900: '#96230B',
            950: '#520F03',
        },
        colorScheme: {
            light: {
                primary: {
                    color: '#FF7253',
                    inverseColor: '#ffffff',
                    hoverColor: '#F05633',
                    activeColor: '#D9401F',
                },
                highlight: {
                    background: 'rgba(255, 114, 83, 0.12)',
                    focusBackground: 'rgba(255, 114, 83, 0.20)',
                    color: '#D9401F',
                    focusColor: '#B82E11',
                },
            },
            dark: {
                primary: {
                    color: '#FF7253',
                    inverseColor: '#ffffff',
                    hoverColor: '#FF8A6E',
                    activeColor: '#FF5C38',
                },
                highlight: {
                    background: 'rgba(255, 114, 83, 0.16)',
                    focusBackground: 'rgba(255, 114, 83, 0.24)',
                    color: '#FFA18B',
                    focusColor: '#FFB8A6',
                },
            },
        },
    },
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(PrimeVue, {
            theme: {
                preset: LifeInRollsPreset,
                options: {
                    darkModeSelector: '.dark',
                },
            },
        });
        app.use(ToastService);
        app.use(ConfirmationService);
        app.directive('tooltip', Tooltip);

        app.mount(el);
    },
    progress: {
        color: '#FF7253',
    },
});

// Set light / dark mode on page load...
initializeTheme();

// Listen for flash toast data from the server...
initializeFlashToast();
