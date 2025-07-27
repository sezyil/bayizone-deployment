<script setup lang="ts">
import { useViewWrapper } from '../stores/viewWrapper';
const logo = "/images/bayizone_light.png";
const viewWrapper = useViewWrapper();
const isLoading = computed(() => viewWrapper.isLoading);

const authMode = computed(() => viewWrapper.authPageMode)

const leftImage = computed(() => {
    return authMode.value === 'login' ? '/images/bayizone-app-login.svg' : '/images/bayizone-app-register.svg';
})

</script>
<template>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
            <div class="flex-1 bg-gradient-to-b from-zinc-900 to-zinc-600 text-center hidden lg:flex">
                <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat"
                    :style="{ backgroundImage: 'url(' + leftImage + ')' }">
                </div>
            </div>
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div>
                    <img :src="logo" class="w-32 mx-auto" />
                </div>
                <div>
                    <!-- Loader -->
                    <div class="w-full h-full fixed top-0 left-0 bg-white opacity-75 z-50" v-if="isLoading">
                        <div class="flex justify-center items-center mt-[50vh]">
                            <i class="icon-spinner9 spinner text-body icon-3x"></i>
                        </div>
                    </div>
                    <RouterView v-slot="{ Component }">
                        <Transition name="fade-fast" mode="out-in">
                            <component :is="Component" />
                        </Transition>
                    </RouterView>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
@tailwind base;
@tailwind components;
@tailwind utilities;

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}

/* spinner */
.spinner {
    -webkit-animation: spin 1s linear infinite;
    animation: spin 1s linear infinite;
}

@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}

@keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}
</style>