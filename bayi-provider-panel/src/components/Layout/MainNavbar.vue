<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { toggleMobileMenu } from '/@src/utils/theme_util';
import { useUserSession } from '/@src/stores/userSession';
const { t } = useI18n();
const viewWrapper = useViewWrapper();
const userSession = useUserSession();
const companyCode = computed(() => userSession.user?.company_code)
const cartActive = computed(() => viewWrapper.cartActive)
const togglerElement = ref<HTMLElement | null>(null);
const toggleSidebar = () => toggleMobileMenu()
</script>
<template>
  <div class="navbar navbar-expand-lg navbar-light">
    <div class="d-flex flex-1 d-lg-none">
      <button class="navbar-toggler sidebar-mobile-main-toggle" type="button" @click="toggleSidebar">
        <i class="icon-transmission"></i>
      </button>
      <span class="badge badge-info my-3 my-lg-0">{{ t('common.provider') }}:
        #{{ companyCode }}
      </span>

      <!-- <button data-target="#navbar-search" type="button" class="navbar-toggler" data-toggle="collapse">
        <i class="icon-search4"></i>
      </button> -->

    </div>

    <div class="navbar-collapse collapse flex-lg-1 order-2 order-lg-1" id="navbar-search">
      <!-- Provider Badge -->
      <span class="badge badge-info my-3 my-lg-0">
        {{ t('common.provider') }} : #{{ companyCode }}
      </span>
    </div>

    <div class="d-flex justify-content-end align-items-center flex-1 flex-lg-0 order-1 order-lg-2">
      <ul class="navbar-nav flex-row">

        <DropdownCart v-if="cartActive" />
        <DropdownLanguage />
        <DropdownUser />
      </ul>
    </div>
  </div>
</template>

<style lang="scss" scoped></style>
