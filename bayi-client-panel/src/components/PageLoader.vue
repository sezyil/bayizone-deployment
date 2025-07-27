<template>
  <div class="card-overlay" v-if="viewWrapper.isLoading"><i class="icon-spinner9 spinner text-body icon-3x"></i></div>
</template>

<script setup lang="ts">
import { useViewWrapper } from '../stores/viewWrapper';

const viewWrapper = useViewWrapper();
</script>

<style lang="scss" scoped></style>
