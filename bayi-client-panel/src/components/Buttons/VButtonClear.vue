<script setup lang="ts">
const emit = defineEmits(['click'])
</script>
<template>
    <VButton icon="fas fa-times" @click="emit('click')" class="btn btn-outline-dark mr-1" text="Temizle" />
</template>


<style scoped></style>