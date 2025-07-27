<script setup lang="ts">
import { useI18n } from 'vue-i18n';
const { t } = useI18n()
const emit = defineEmits(['click'])
interface Props {
    type?: HTMLButtonElement['type'],
    class?: string;
    text?: string;
    disabled?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    type: 'button',
    class: 'btn-primary',
    text: t('actions.save'),
    disabled: false,
})
</script>
<template>
    <VButton @click="$emit('click')" :class="props.class" :text="text" :disabled="disabled" :type="props.type" />
</template>
<style scoped></style>