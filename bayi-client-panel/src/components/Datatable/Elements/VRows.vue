<template>
    <tr :id="rowIdGenerator(row)" v-for="(row, index) in rowData" :key="index">
        <VCells :cell="cell" v-for="(cell, cellIndex) in row" :key="cell.id" :head-text="tableHeads[cellIndex]" @triggeredevent="emitTriggered" />
    </tr>
</template>

<script setup lang="ts">
//@ts-nocheck
const emit = defineEmits(['triggeredevent']);
const props = defineProps({
    rowData: {
        default: [],
        type: Array
    },
    tableHeads: {
        default: [],
        type: Array
    }
});

const emitTriggered = (args: any) => emit("triggeredevent", args)


const rowIdGenerator = (e: any) => {
    return (typeof e['id'] !== "undefined" ? `vData-${e['id']}` : ``)
}

</script>

<style scoped>
@import "../datatable.css";
</style>
