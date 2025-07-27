<script setup lang="ts">
const emit = defineEmits(['click']);
interface Props {
    icon?: string;
    text?: string;
    disabled?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    icon: 'fas fa-plus',
    text: 'Yeni',
    disabled: false,
})
</script>
<template>
    <VButton :icon="icon" @click="$emit('click')" class="btn-bayi-red" :text="text" :disabled="disabled" />
</template>
<style scoped></style>