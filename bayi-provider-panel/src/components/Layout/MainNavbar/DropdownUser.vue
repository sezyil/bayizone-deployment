<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useUserSession } from '/@src/stores/userSession';
const userSession = useUserSession();
const { t } = useI18n();

</script>
<template>
    <li class="nav-item nav-item-dropdown-lg dropdown dropdown-user h-100">
        <a href="#"
            class="navbar-nav-link navbar-nav-link-toggler dropdown-toggle d-inline-flex align-items-center h-100"
            data-toggle="dropdown">
            <img :src="userSession.getCustomerImage" class="rounded-pill mr-lg-2" alt="" height="34">
            <span class="d-none d-lg-inline-block">{{ userSession.user?.fullname }}</span>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <RouterLink to="/app/profile" class="dropdown-item"><i class="icon-cog5"></i> {{ t('account.settings') }}</RouterLink>
            <a href="#" class="dropdown-item" @click.prevent="userSession.logoutUser()"><i class="icon-switch2"></i>
                {{ t('actions.logout') }}</a>
        </div>
    </li>
</template>

<style lang="scss" scoped></style>