<template>
    <table class="table align-items-center table-flush">
        <VTableHead :table-heads="tableHeads" />
        <tbody>
            <VRows :row-data="currentData" @triggeredevent="emitTriggered" />
        </tbody>
    </table>
</template>

<script setup lang="ts">

const emit = defineEmits(['triggeredevent']);
const props = defineProps({
    tableHeads: {
        default: [],
        type: Array
    },
    currentData: {
        default: [],
        type: Array
    },
});

const emitTriggered = (args: any) => {
    emit("triggeredevent", args)
}
</script>

<style scoped>
@import "../datatable.css";
</style>
