<template>
    <div class="card-overlay" v-if="active"><i class="icon-spinner9 spinner text-body icon-3x"></i></div>
</template>
  
<script setup lang="ts">
const props = defineProps({
    active: Boolean,
    icon: {
        type: String,
        default: "fa fa-sync lds-dual-ring mr-2"
    }
})
</script>