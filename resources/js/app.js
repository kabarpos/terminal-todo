import "./bootstrap";
import "../css/app.css";

import { createApp, h, defineAsyncComponent } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from '@/../../vendor/tightenco/ziggy';
import axios from 'axios';

// Setup Axios CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Interceptor untuk refresh halaman jika mendapat 419
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            window.location.reload();
            return Promise.reject(error);
        }
        return Promise.reject(error);
    }
);

// Inisialisasi Dark Mode
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark')
} else {
    document.documentElement.classList.remove('dark')
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Lazy load heavy components
        app.component('UserManagement', defineAsyncComponent(() => 
            import('./Pages/Users/Index.vue')
        ));
        app.component('RoleManagement', defineAsyncComponent(() => 
            import('./Pages/Roles/Index.vue')
        ));
        app.component('ProfileSettings', defineAsyncComponent(() => 
            import('./Pages/Profile/Edit.vue')
        ));

        app.use(plugin);
        app.use(ZiggyVue);
        
        return app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
