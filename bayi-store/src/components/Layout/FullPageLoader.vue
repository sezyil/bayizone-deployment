<script setup lang="ts">
import { useI18n } from 'vue-i18n';
type LoaderType = 'product' | 'page';
const { t } = useI18n();
const maxDotCount = 3;
const props = withDefaults(defineProps<{
    type: LoaderType;
}>(), {
    type: 'page'
});
const title = computed(() => {
    return props.type === 'product' ? t('common.productLoading') : t('common.storeLoading');
});
const loadingTitle = ref<string>('');

onMounted(() => {
    loadingTitle.value = title.value; // set initial title
    let dotCount = 0;
    const interval = setInterval(() => {
        dotCount++;
        if (dotCount > maxDotCount) {
            dotCount = 0;
        }
        loadingTitle.value = title.value + '.'.repeat(dotCount);
    }, 500);

    onBeforeUnmount(() => {
        clearInterval(interval);
    });
});

</script>
<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-gray-900"></div>
        <div class="text-gray-900 text-2xl font-semibold mt-4">{{ loadingTitle }}</div>
    </div>
</template>


<style scoped></style>