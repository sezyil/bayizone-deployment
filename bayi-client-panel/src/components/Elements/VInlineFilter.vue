<script setup lang="ts">
interface VInlineFilterEmits {
    (event: 'search'): void;
    (event: 'clear'): void;
}
const emit = defineEmits<VInlineFilterEmits>();

</script>
<template>
    <div class="card">
        <div class="card-body">
            <div>
                <slot />
            </div>

            <div class="row mt-2">
                <!-- search button left aligned -->
                <div class="col-md-12 d-flex justify-content-end">
                    <slot name="beforeSearch"></slot>
                    <!-- clear -->
                    <VButtonClear @click="$emit('clear')" />
                    <VButtonSearch @click="$emit('search')" />
                    <slot name="afterSearch"></slot>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped></style>