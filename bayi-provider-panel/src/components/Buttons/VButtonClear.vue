<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const emit = defineEmits(['click'])
const { t } = useI18n()
</script>
<template>
    <VButton icon="fas fa-times" @click="emit('click')" class="btn btn-outline-secondary mr-1"
        :text="t('actions.clear')" />
</template>


<style scoped></style>