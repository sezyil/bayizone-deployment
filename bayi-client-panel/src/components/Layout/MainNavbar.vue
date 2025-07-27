<script setup lang="ts">
import { toggleMobileMenu } from '/@src/utils/theme_util';

const togglerElement = ref<HTMLElement | null>(null);
const toggleSidebar = () => toggleMobileMenu()
</script>
<template>
  <div class="navbar navbar-expand-xl navbar-light">
    <div class="d-flex flex-1 d-xl-none">
      <button class="navbar-toggler sidebar-mobile-main-toggle" type="button" @click="toggleSidebar">
        <i class="icon-transmission"></i>
      </button>

      <!-- <button data-target="#navbar-search" type="button" class="navbar-toggler" data-toggle="collapse">
        <i class="icon-search4"></i>
      </button> -->

    </div>

    <div class="navbar-collapse collapse flex-lg-1 order-2 order-lg-1 invisible" id="navbar-search">
      <div class="navbar-search d-flex align-items-center py-2 py-lg-0">
        <div class="form-group-feedback form-group-feedback-left flex-grow-1">
          <input type="text" class="form-control" placeholder="Ara" />
          <div class="form-control-feedback">
            <i class="icon-search4 opacity-50"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end align-items-center flex-1 flex-lg-0 order-1 order-lg-2">
      <ul class="navbar-nav flex-row">

        <!-- <DropdownMessages /> -->

        <!-- <li class="nav-item nav-item-dropdown-lg dropdown">
          <a href="#" class="navbar-nav-link navbar-nav-link-toggler" data-toggle="dropdown">
            <i class="icon-grid6"></i>
          </a>

          <div class="dropdown-menu dropdown-menu-right dropdown-content wmin-lg-350">
            <div class="dropdown-content-body p-2">
              <div class="row no-gutters">
                <div class="col-4">
                  <a href="#" class="btn btn-outline-light border-transparent text-body btn-block py-2">
                    <i class="icon-github4 icon-2x mt-1"></i>
                    <div class="font-weight-semibold mt-1">Github</div>
                  </a>

                  <a href="#" class="btn btn-outline-light border-transparent text-body btn-block py-2">
                    <i class="icon-dropbox text-primary icon-2x mt-1"></i>
                    <div class="font-weight-semibold mt-1">Dropbox</div>
                  </a>
                </div>

                <div class="col-4">
                  <a href="#" class="btn btn-outline-light border-transparent text-body btn-block py-2">
                    <i class="icon-dribbble3 text-pink icon-2x mt-1"></i>
                    <div class="font-weight-semibold mt-1">Dribbble</div>
                  </a>

                  <a href="#" class="btn btn-outline-light border-transparent text-body btn-block py-2">
                    <i class="icon-google-drive text-success icon-2x mt-1"></i>
                    <div class="font-weight-semibold mt-1">Drive</div>
                  </a>
                </div>

                <div class="col-4">
                  <a href="#" class="btn btn-outline-light border-transparent text-body btn-block py-2">
                    <i class="icon-twitter text-info icon-2x mt-1"></i>
                    <div class="font-weight-semibold mt-1">Twitter</div>
                  </a>

                  <a href="#" class="btn btn-outline-light border-transparent text-body btn-block py-2">
                    <i class="icon-youtube text-danger icon-2x mt-1"></i>
                    <div class="font-weight-semibold mt-1">Youtube</div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </li> -->

        <DropdownUser />
      </ul>
    </div>
  </div>
</template>

<style lang="scss" scoped></style>
