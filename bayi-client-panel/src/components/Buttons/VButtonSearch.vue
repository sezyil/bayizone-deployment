<script setup lang="ts">
const emit = defineEmits(['click'])
</script>
<template>
    <VButton icon="fas fa-search" @click="emit('click')" class="btn-bayi-red" text="Ara" />
</template>


<style scoped></style>