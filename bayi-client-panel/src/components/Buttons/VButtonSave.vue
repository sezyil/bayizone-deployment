<script setup lang="ts">
const emit = defineEmits(['click'])
interface Props {
    type?: HTMLButtonElement['type'],
    class?: string;
    text?: string;
    disabled?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    type: 'button',
    class: 'btn-bayi-red',
    text: 'Kaydet',
    disabled: false,
})
</script>
<template>
    <VButton @click="$emit('click')" :class="props.class" :text="text" :disabled="disabled" :type="props.type" />
</template>
<style scoped></style>