<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IStoreProductDetail } from '/@src/shared/product';
const props = defineProps<{ detail: IStoreProductDetail }>();
const { t } = useI18n();
</script>
<template>
    <div class="bg-white rounded-lg overflow-hidden">
        <div class="p-4">
            <h2 class="text-gray-800 text-lg font-semibold">{{ t('product.details') }}</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 p-4">
            <div>
                <p class="text-gray-600 font-semibold">{{ t('product.length') }}:</p>
                <p class="text-gray-800">{{ detail.length }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">{{ t('product.width') }}:</p>
                <p class="text-gray-800">{{ detail.width }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">{{ t('product.height') }}:</p>
                <p class="text-gray-800">{{ detail.height }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">{{ t('product.weight') }}:</p>
                <p class="text-gray-800">{{ detail.weight }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">{{ t('product.volume') }}:</p>
                <p class="text-gray-800">{{ detail.volume }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">{{ t('product.package') }}:</p>
                <p class="text-gray-800">{{ detail.package }}</p>
            </div>
        </div>
    </div>
</template>



<style scoped></style>