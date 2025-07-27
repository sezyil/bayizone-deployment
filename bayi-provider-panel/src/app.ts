import { createApp as createClientApp } from 'vue'
import { createPinia } from 'pinia'
import { createHead } from '@vueuse/head'
import VueEasyLightbox from 'vue-easy-lightbox'
import './assets/css/icons/fontawesome/styles.min.css';
import './assets/css/icons/icomoon/styles.min.css';
import VueSweetalert2 from 'vue-sweetalert2';
import PrimeVue from 'primevue/config';
import VueSelect from "vue-select";
import 'primevue/resources/themes/aura-light-green/theme.css'
import "vue-select/dist/vue-select.css";

import VueUploadComponent from 'vue-upload-component';


import BayizoneApp from './BayizoneApp.vue'
import { createRouter } from './router';
import { useApi } from './composables/useApi';

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


    app.component("v-select", VueSelect);
    app.use(VueSweetalert2);
    app.component('file-upload', VueUploadComponent)
    app.use(PrimeVue);




    app.use(bayizone.router)

    return bayizone
}
