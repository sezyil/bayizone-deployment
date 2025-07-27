<route lang="yaml">
meta:
    requiresAuth: true
</route>
<script setup lang="ts">
import '/@src/assets/css/all.min.css';
import '/@src/assets/css/main.css';
</script>
<template>
    <AppLayout>
        <!-- Content Wrapper -->
        <RouterView v-slot="{ Component }">
            <Transition name="fade-fast" mode="out-in">
                <component :is="Component" />
            </Transition>
        </RouterView>
    </AppLayout>
</template>
