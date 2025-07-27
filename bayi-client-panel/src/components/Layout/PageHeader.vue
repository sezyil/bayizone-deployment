<script setup lang="ts">
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();

const router = useRouter()

</script>

<template>
  <div class="page-header">
    <div class="page-header-content header-elements-xl-inline">
      <div class="page-title d-flex">
        <h4>
          <i class="icon-arrow-left52 mr-2 cursor-pointer text-secondary" @click.prevent="router.go(-1)"></i>
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
