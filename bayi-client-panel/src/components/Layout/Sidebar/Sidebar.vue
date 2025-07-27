<script setup lang="ts">
import { useUserSession } from "../../../stores/userSession";
import $ from "jquery";
import SidebarContent from "./SidebarContent.vue";
import { useViewWrapper } from "/@src/stores/viewWrapper";
import { SwalInstance } from "/@src/shared/common/type-swal";
import { toggleMobileMenu } from "/@src/utils/theme_util";
import { useSwal } from "/@src/composables/useSwal";
const swal = useSwal();

const userSession = useUserSession();

const viewWrapper = useViewWrapper();

const sidebarmain = ref<HTMLElement | null>(null);
const resizeClass = "sidebar-main-resized",
  unfoldClass = "sidebar-main-unfold";

const unfoldDelay = 150;
const timerStart = ref<any>(null),
  timerFinish = ref<any>(null);

const sidebarClass = computed(() => (viewWrapper.isPushed ? `sidebar-main-resized` : ""));

const sidebarMouseOut = () => {
  clearTimeout(timerStart.value);
  timerFinish.value = setTimeout(function () {
    if (sidebarmain.value) {
      sidebarmain.value.classList.remove(unfoldClass);
    }
  }, unfoldDelay);
};

const sidebarMouseOver = () => {
  clearTimeout(timerFinish.value);
  timerStart.value = setTimeout(function () {
    if (sidebarmain.value) {
      sidebarmain.value.classList.contains(resizeClass)
        ? sidebarmain.value.classList.add(unfoldClass)
        : ``;
    }
  }, unfoldDelay);
};
const toggleSidebar = () => viewWrapper.setPushed(viewWrapper.isPushed ? false : true);
</script>
<template>
  <div class="sidebar sidebar-dark sidebar-main sidebar-expand-xl" :class="sidebarClass" ref="sidebarmain"
    @mouseover="sidebarMouseOver()" @mouseout="sidebarMouseOut()">
    <!-- App logo and controls -->
    <div class="navbar navbar-dark bg-dark-100 navbar-static border-0">
      <div class="navbar-brand flex-fill wmin-0">
        <a href="/" class="d-inline-block">
          <img src="/images/bayizone_black.png" class="sidebar-resize-hide" alt="" />
          <img src="/images/logo_icon_light.png" class="sidebar-resize-show" alt="" />
        </a>
      </div>

      <ul class="navbar-nav align-self-center ml-auto sidebar-resize-hide">
        <li class="nav-item dropdown">
          <button type="button" @click="toggleSidebar"
            class="btn btn-outline-light-100 text-white border-transparent btn-icon rounded-pill btn-sm sidebar-control sidebar-main-resize d-lg-inline-flex">
            <i class="icon-transmission"></i>
          </button>

          <!-- <button type="button" @click.prevent="toggleMobileMenu(true)"
            class="btn btn-outline-light-100 text-white border-transparent btn-icon rounded-pill btn-sm sidebar-mobile-main-toggle d-lg-none">
            <i class="icon-cross2"></i>
          </button> -->
        </li>
      </ul>
    </div>
    <!-- /app logo and controls -->

    <!-- Sidebar content -->
    <SidebarContent />
    <!-- /sidebar content -->
  </div>
</template>

<style lang="scss" scoped></style>
