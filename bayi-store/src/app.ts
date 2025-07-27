import { createApp as createClientApp } from 'vue'
import { createPinia } from 'pinia'
import { createHead } from '@vueuse/head'
import VueSweetalert2 from 'vue-sweetalert2';
import BayizoneApp from './BayizoneApp.vue'
import { createRouter } from './router';
import { useApi } from './composables/useApi';
import PrimeVue from 'primevue/config';
import VueEasyLightbox from 'vue-easy-lightbox'

import 'vue-easy-lightbox/external-css/vue-easy-lightbox.css'
import '/@src/assets/css/app.css'
import 'primevue/resources/themes/viva-light/theme.css'
export type BayizoneAppContext = Awaited<ReturnType<typeof createApp>>
export type BayizonePlugin = (vuero: BayizoneAppContext) => void | Promise<void>
const plugins = import.meta.glob<{ default: BayizonePlugin }>('./plugins/*.ts', {
    eager: true,
})

// this is a helper function to define plugins with autocompletion
export function definePlugin(plugin: BayizonePlugin) {
    return plugin
}


export async function createApp() {
    const app = createClientApp(BayizoneApp)
    const router = createRouter()
    const api = useApi()

    const head = createHead()
    app.use(head)

    const pinia = createPinia()

    app.use(pinia)

    app.use(PrimeVue, { ripple: true });
    app.use(VueEasyLightbox)

    const bayizone = {
        app,
        api,
        router,
        head,
        pinia,
    }

    app.provide('bayizone', bayizone)
    for (const path in plugins) {
        try {
            const { default: plugin } = plugins[path]
            await plugin(bayizone)
        } catch (error) {
            console.error(`Error while loading plugin "${path}".`)
            console.error(error)
        }
    }

    app.use(VueSweetalert2);
    app.use(bayizone.router)

    return bayizone
}
