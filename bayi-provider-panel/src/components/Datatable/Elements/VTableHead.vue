<template>
    <thead>
        <tr>
            <th scope="col" v-for="(item, index) in tableHeads" :key="index">{{ item }}</th>
        </tr>
    </thead>
</template>

<script setup lang="ts">
const props = defineProps({
    tableHeads: {
        default: [],
        type: Array
    },
});
</script>
<style scoped>
@import "../datatable.css";
</style>
