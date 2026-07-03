import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const appName = import.meta.env.VITE_APP_NAME || 'NESpresso';

// Initialize theme before creating the app
const initializeTheme = () => {
    const saved = localStorage.getItem('theme');
    if (saved === 'coffee' || saved === 'nes') {
        document.documentElement.setAttribute('data-theme', saved);
    } else {
        document.documentElement.setAttribute('data-theme', 'coffee');
    }
};

// Initialize immediately
initializeTheme();

createInertiaApp({
    title: () => `${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const Root = {
            render() {
                return [
                    h(App, props),
                    h(Toaster, {
                        position: 'top-right',
                        theme: 'dark',
                        closeButton: true,
                        toastOptions: {
                            style: {
                                background: 'var(--color-surface)',
                                color: 'var(--color-text-primary)',
                                border: '1px solid var(--color-border)',
                            },
                        },
                    }),
                ];
            },
        };
        return createApp(Root)
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
