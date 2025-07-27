<script setup lang="ts">

</script>
<template>
    <div>
        <StoreNavbar />
        <div class="content-area">
            <slot />
        </div>
        <CartModal />
        <StoreFooter />
    </div>
</template>


<style scoped></style>