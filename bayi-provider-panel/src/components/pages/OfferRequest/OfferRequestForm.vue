<script setup lang="ts">
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();
const isLoading = ref(false);


onMounted(async () => {
    viewWrapper.setCartActive(true);
});

onUnmounted(() => {
    viewWrapper.setCartActive(false);
});
</script>
<template>
    <Loader v-if="isLoading" />
    <OfferProductWrapper />
</template>

<style scoped></style>