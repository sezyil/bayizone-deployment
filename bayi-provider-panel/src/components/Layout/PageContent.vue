<template>
    <div class="content-inner">
        <PageHeader>
            <slot name="header" />
        </PageHeader>
        <div class="content pt-0">
            <slot />
        </div>
        <Footer />
    </div>
</template>

<script setup lang="ts">

</script>

<style scoped></style>