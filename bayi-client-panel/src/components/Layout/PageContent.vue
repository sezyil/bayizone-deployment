<script setup lang="ts">
const props = defineProps({
    headerDisabled: {
        type: Boolean,
        default: false
    }
});

const slots=defineSlots();


</script>
<template>
    <div class="content-inner">
        <PageHeader v-if="!headerDisabled">
            <slot name="header" />
        </PageHeader>
        <div v-if="$slots.customheader">
            <slot name="customheader" />
        </div>
        <div class="content pt-0">
            <slot />
        </div>
        <Footer />
    </div>
</template>



<style scoped></style>