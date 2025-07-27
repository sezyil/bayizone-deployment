<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const emit = defineEmits(['click'])
const { t } = useI18n()
</script>
<template>
    <VButton icon="fas fa-search" @click="emit('click')" class="btn-primary" :text="t('actions.search')" />
</template>


<style scoped></style>