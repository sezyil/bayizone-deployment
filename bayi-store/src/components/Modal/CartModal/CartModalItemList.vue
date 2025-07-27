<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useShopStore } from '/@src/stores/shopStore';
const { t, locale } = useI18n();
const currentLang = computed(() => locale.value);
const store = useShopStore();
import { AvailableLanguages } from '/@src/shared/common/common-language';
</script>
<template>
    <div>
        <div class="flex justify-between items-center">
            <h3 class="text-xl ">{{ t('common.cart') }} <span class="text-sm">({{ store.cartList.length }}
                    {{ t('common.product') }})</span>
            </h3>
            <!-- only trash svg -->

            <span class="pi pi-trash text-slate-700 text-2xl cursor-pointer" @click="store.clearCart"></span>

        </div>
        <div class="flex flex-col space-y-2 mt-2">
            <div class="flex flex-col space-y-1" v-if="store.cartList.length">
                <div class="flex items-center justify-between bg-white p-4 rounded-md gap-3 border border-gray-200"
                    v-for="item in store.cartList" :key="item.uuid">
                    <img :src="item.images[0].itemImageSrc" alt="product image"
                        class="w-1/4 h-16 object-cover rounded-md">
                    <h4 class="text-sm font-semibold text-wrap w-1/4 ">
                        <p class="break-words">{{ item.name }}</p>
                        <div v-if="item.selected_color?.length || item.selected_dimension?.length"
                            class="flex flex-wrap gap-2">
                            <span v-for="color in item.selected_color"
                                class="bg-gray-200 text-gray-800 text-xs font-normal me-2 px-2.5 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                                <b>{{ color.variant.name }}:</b>
                                {{ color.variant_value.name }}
                            </span>
                            <span v-for="dimension in item.selected_dimension"
                                class="bg-gray-200 text-gray-800 text-xs font-normal px-2.5 py-1 rounded dark:bg-gray-700 dark:text-gray-300 flex flex-col">
                                <b>{{ dimension.variant.name }}:</b>
                                {{ t('product.selectedDimension', {
                                    width: dimension.variant_value.value.width,
                                    height: dimension.variant_value.value.height,
                                    length: dimension.variant_value.value.length
                                }) }}
                            </span>
                        </div>
                    </h4>

                    <input type="number" v-model="item.quantity"
                        class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400 w-1/4 text-wrap max-w-[80px]">

                    <button @click="store.removeItem(item)"
                        class="bg-red-400 border border-red-400 p-2 text-sm font-medium text-white rounded-md hover:bg-red-500 focus:outline-none focus:border-red-500 focus:ring focus:red-red-300 focus:ring-opacity-50 focus:ring-offset-2 focus:ring-offset-red-400 flex items-center justify-center">
                        <span class="pi pi-trash"></span>
                    </button>
                </div>
            </div>
            <div class="flex flex-col space-y-1" v-else>
                <!-- noitem -->
                <div class="flex items center justify-center p-4 rounded-md border border-gray-300">
                    <p class="text-sm text-gray-500">{{ t('components.cart.empty') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>