<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useShopStore } from '/@src/stores/shopStore';
import { GLOB_URIS } from '/@src/utils/GLOB_URIS';
import { IStoreCartData } from '/@src/shared/cart';
const store = useShopStore();
const props = defineProps({
    storeId: {
        type: String,
        required: true
    },
    product: {
        type: Object as PropType<IStoreCartData>,
        required: true
    }
});
const { t } = useI18n();

const getImage = computed(() => {
    return props.product.images[0].itemImageSrc ?? GLOB_URIS.NO_IMAGE;
})

const redirectUri = computed(() => {
    return `/${props.storeId}/product/${props.product.uuid}`;
})

const isInList = computed(() => {
    return store.cartList.some((item) => item.uuid === props.product.uuid);
})

onMounted(() => { })
</script>
<template>
    <div class="w-full md:w-1/3 xl:w-1/4 p-6 flex flex-col border-1 rounded-sm border-gray-300">
        <!-- set middle -->


        <div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg"
            :id="`product-item-${product.uuid}`">

            <div class="relative">
                

                <span
                    class="ml-1 mt-1 absolute bg-zinc-100 text-zinc-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-zinc-400 border border-zinc-400">
                    <span class="pi pi-eye mr-1"></span>
                    {{ props.product.view_count }}
                </span>
                <span v-if="store.getStore?.ai_support"
                    class="mr-1 mt-1 absolute right-0 bg-zinc-100 text-zinc-800 text-xs font-medium inline-flex items-center px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-zinc-400 border border-zinc-400">
                    Discover similar
                    <div class="ps-widget-5384 ml-1" :data-pid="product.uuid"></div>
                </span>


                <RouterLink :to="redirectUri">

                    <img class="pb-2 rounded-t-lg  object-cover object-center w-full h-64" :src="getImage"
                        alt="product image" />
                </RouterLink>
            </div>

            <div class="px-5 pb-5">

                <RouterLink :to="redirectUri">
                    <h5 class="text-xl text-left font-semibold tracking-tight text-gray-900 dark:text-white">
                        {{ props.product.name }}
                    </h5>
                </RouterLink>



                <!-- width etc -->

                <div class="flex items-center justify-start mt-3">
                    <!-- go to detail -->
                    <RouterLink :to="redirectUri"
                        class="text-gray-700 bg-white border border-gray-700 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center 
                        flex items-center dark:text-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="pi pi-eye mr-2"></span> {{ t('common.detail') }}
                    </RouterLink>
                    <!-- remove from cart -->
                </div>






            </div>
        </div>

    </div>
</template>


<style scoped></style>