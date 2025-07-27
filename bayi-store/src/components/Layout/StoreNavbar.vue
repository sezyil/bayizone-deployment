<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useShopStore } from '/@src/stores/shopStore';
const { t } = useI18n();

const store = useShopStore();
const storeDetail = computed(() => store.getStore)
const navheader = ref<HTMLElement | null>(null);
const homeRedirect = computed(() => {
    return storeDetail.value?.id ? `/${storeDetail.value.id}` : '/';
})
const visibleRef = ref(false);

const itemCount = computed(() => {
    return store.cartList.length;
})
const menuToggled = ref(false);
let lastScroll = 0;

const stickyHandler = () => {
    const currentScroll = window.scrollY || document.documentElement.scrollTop;
    const calculateHeight = navheader.value?.offsetHeight || 0;
    const __class = 'sticky-header';
    /* with time interval */
    if (currentScroll > calculateHeight) {
        navheader.value?.classList.add(__class);
    } else {
        navheader.value?.classList.remove(__class);

    }
    lastScroll = currentScroll;
}

onMounted(() => {
    window.addEventListener('scroll', stickyHandler);
})

</script>
<template>
    <nav id="header" class="w-full z-30 top-0 py-1 pb-0" ref="navheader">
        <!-- topbar -->
        <div class="w-11/12 mx-auto flex flex-wrap items-center justify-between mt-0 px-6">
            <!-- image -->
            <div class="flex items-center order-1 md:order-2">
                <a href="https://bayizone.com" target="_blank"
                    class="flex items-center tracking-wide no-underline hover:no-underline font-bold text-gray-800 text-xl">
                    <img class="h-12" src="/images/bayizone_light.png" alt="logo" />
                </a>
            </div>

            <div class="order-2 md:order-3 flex items-center gap-1" id="topbar-content">
                <LanguageSwitcher />

            </div>
        </div>

        <!-- header -->
        <div class="w-full mx-auto bg-gray-100">
            <div class="w-11/12 mx-auto flex flex-wrap items-center justify-between mt-0 px-6 py-3 ">

                <label for="menu-toggle" class="cursor-pointer block">
                    <RouterLink :to="homeRedirect">
                        {{ t('common.products') }}
                    </RouterLink>
                </label>


                <div class="order-1 md:order-2">
                    <RouterLink
                        class="flex items-center tracking-wide no-underline hover:no-underline font-bold text-gray-800 text-xl "
                        :to="homeRedirect">
                        {{ storeDetail?.name }}
                    </RouterLink>
                </div>

                <div class="order-2 md:order-3 flex items-center gap-1" id="nav-content">
                    <!-- search toggle -->
                    <NavSearchDropdown />
                    <div class="ps-widget-5382"></div>

                    <button class="pl-3 inline-block no-underline hover:text-black relative" id="cartAction"
                        @click.prevent="store.setModalActive(true)">
                        <!-- cart icon with count -->
                        <span class="pi pi-shopping-cart text-xl"></span>
                        <span
                            class="absolute top-0 right-0 mt-1 transform translate-x-1/2 -translate-y-1/2 bg-slate-500 rounded-full w-4 h-4 flex items-center justify-center text-xs text-white">{{ itemCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>


    </nav>



</template>

<style lang="css" scoped>
/* sticky with animations */
.sticky-header {
    @apply sticky top-0 left-0 right-0 bg-white shadow-md z-50 transition-all duration-300 ease-in-out;
}
</style>