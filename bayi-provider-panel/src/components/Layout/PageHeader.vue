<script setup lang="ts">
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();
const prevActive = computed(() => viewWrapper.previousButton)
const router = useRouter()

</script>

<template>
  <div class="page-header">
    <div class="page-header-content header-elements-lg-inline">
      <div class="page-title d-flex">
        <h4>
          <i class="icon-arrow-left52 mr-2 cursor-pointer text-indigo" @click.prevent="router.go(-1)"
            v-if="prevActive"></i>
          <span class="font-weight-semibold">{{ viewWrapper.pageTitle }}</span>
        </h4>
      </div>

      <HeaderButtonsWrapper>
        <slot />
      </HeaderButtonsWrapper>

    </div>
  </div>
</template>

<style lang="scss" scoped></style>
