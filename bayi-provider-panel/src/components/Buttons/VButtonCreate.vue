<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const emit = defineEmits(['click']);
interface Props {
    icon?: string;
    text?: string;
    disabled?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    icon: 'fas fa-plus',
    text: t('actions.create'),
    disabled: false,
})
</script>
<template>
    <VButton :icon="icon" @click="$emit('click')" class="btn-primary" :text="text" :disabled="disabled" />
</template>
<style scoped></style>