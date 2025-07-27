<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useUserSession } from '/@src/stores/userSession';
import { GLOB_URIS } from '/@src/utils/global_uris';
const { t } = useI18n()
const customerId = computed(() => useUserSession().user?.customer_id);
</script>

<template>
  <div class="sidebar-content">
    <!-- User menu -->
    <SidebarUserSection />

    <!-- Main navigation -->
    <div class="sidebar-section">
      <ul class="nav nav-sidebar" data-nav-type="accordion">
        <!-- Main -->
        <SidebarMenuWrapper>
          <SidebarItem :title="t('sidemenu.dashboard')" icon="icon-home4" uri="/" />
          <!-- /catalog -->
          <!-- orders -->
          <SidebarItem :title="t('sidemenu.orders')" icon="fas fa-shopping-cart" uri="/app/orders" />
          <SidebarItem :title="t('sidemenu.shipments')" icon="fas fa-truck" uri="/app/shipments" />
          <!-- offers -->
          <SidebarSubListItem :title="t('sidemenu.offer')" icon="fas fa-file-invoice-dollar">
            <SidebarItem :title="t('sidemenu.offer')" icon="fas fa-file-invoice-dollar" uri="/app/offers" />
            <SidebarItem :title="t('sidemenu.offerRequest')" icon="fas fa-file-invoice-dollar"
              uri="/app/offer_requests" />
            <SidebarItem :title="t('sidemenu.createOfferRequest')" icon="fas fa-file-invoice-dollar"
              uri="/app/offer_requests/create" />
          </SidebarSubListItem>
          <!-- users -->
          <SidebarItem :title="t('sidemenu.user')" icon="fas fa-users" uri="/app/users" />
          <!-- /users -->
          <SidebarItem :title="t('sidemenu.warehouse')" icon="fas fa-warehouse" uri="/app/warehouses" />
          <!-- bank_accounts -->
          <SidebarItem :title="t('sidemenu.bankAccounts')" icon="fas fa-university" uri="/app/bank_accounts" />


        </SidebarMenuWrapper>
        <!-- /layout -->
      </ul>
    </div>
    <!-- /main navigation -->
  </div>
</template>

<style lang="scss" scoped></style>
