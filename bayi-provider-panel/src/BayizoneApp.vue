<script setup lang="ts">
// This is the global app setup function

</script>

<template>
  <Suspense>
    <RouterView v-slot="{ Component }">
      <component :is="Component" />
    </RouterView>
  </Suspense>
</template>
