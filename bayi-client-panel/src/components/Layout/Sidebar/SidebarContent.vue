<script setup lang="ts">
import { useUserPermission } from "/@src/composables/useUserPermission";
import { useUserSession } from "/@src/stores/userSession";
import { GLOB_URIS } from "/@src/utils/GLOB_URIS";
const wizardCompleted = computed(() => useUserSession().wizardFinished);
const customerId = computed(() => useUserSession().user?.customer_id);
const __userPermission = useUserPermission();

const storeLink = computed(() => {
  if (customerId.value) {
    let uri = GLOB_URIS.STOREURI.replace("{customerid}", customerId.value);
    return uri;
  }
  else {
    return null;
  }
});

const planData = computed(() => useUserSession().planData);
const {
  value: {
    attribute_group: attributeGroupPermission,
    attribute: attributePermission,
    product: productPermission,
    attribute_value: attributeValuePermission,
    category: categoryPermission,
    option: optionPermission,
    company_customer: companyCustomerPermission,
    company_customer_bank_account: companyCustomerBankAccountPermission,
    company_customer_warehouse: companyCustomerWarehousePermission,
    customer: customerPermission,
    customer_bank_account: customerBankAccountPermission,
    customer_offer: customerOfferPermission,
    permission: permissionPermission,
    user: userPermission,
    unit: unitPermission,
    transaction: transactionPermission,
    customer_order: customerOrderPermission,
    offer_request: offerRequestPermission,
    payment: paymentPermission,
    variant: variantPermission,
  },
} = computed(() => __userPermission.getAll());
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
          <SidebarItem title="Anasayfa" icon="icon-home2" uri="/app" />
          <!-- offers -->
          <SidebarSubListItem v-if="wizardCompleted && customerOfferPermission.view && planData" title="Teklifler"
            icon="fas fa-exchange-alt">
            <SidebarItem v-if="wizardCompleted &&
              (customerOfferPermission.view || customerOfferPermission.create)
            " title="Teklifler" icon="fas fa-exchange-alt" uri="/app/offers" />
            <SidebarItem v-if="wizardCompleted && offerRequestPermission.view" title="Teklif İstekleri"
              icon="fas fa-file-alt" uri="/app/offer_requests" />

          </SidebarSubListItem>
          <SidebarItem v-if="wizardCompleted && planData?.sales_management && customerOrderPermission.create"
            title="Siparişler" icon="fas fa-shopping-cart" uri="/app/customer_orders" />
          <SidebarItem v-if="wizardCompleted && planData?.sales_management && customerOrderPermission.create"
            title="Sevkiyatlar" icon="fas fa-truck" uri="/app/shipments" />
          <!-- store uri -->
          <a v-if="planData?.online_store && storeLink" :href="storeLink" class="nav-link routing" target="_blank">
            <i class="fas fa-store"></i>
            <span>Mağazam</span>
          </a>
          <!-- catalog -->
          <SidebarSubListItem title="Katalog" icon="fas fa-barcode">
            <!-- products -->
            <SidebarItem v-if="productPermission.view || productPermission.create" title="Ürünler" icon="fas fa-boxes"
              uri="/app/catalog/product" />
            <!-- categories -->
            <SidebarItem v-if="categoryPermission.view || categoryPermission.create" title="Kategoriler"
              icon="fas fa-tags" uri="/app/catalog/categories" />
            <!-- attributes -->
            <SidebarSubListItem title="Özellikler" icon="fas fa-info-circle">
              <!-- attribute_groups -->
              <SidebarItem v-if="attributeGroupPermission.view ||
                attributeGroupPermission.create
              " title="Özellik Grupları" uri="/app/catalog/attribute_groups" />
              <!-- attributes -->
              <SidebarItem v-if="attributePermission.view || attributePermission.create" title="Özellikler"
                uri="/app/catalog/attributes" />
            </SidebarSubListItem>
            <!-- /attributes -->
            <!-- product_units -->
            <SidebarItem v-if="unitPermission.view || unitPermission.create" title="Ürün Birimleri"
              icon="fas fa-balance-scale" uri="/app/catalog/product_units" />
            <!-- variants and variant values -->
            <SidebarSubListItem title="Varyantlar" icon="fas fa-cubes">
              <!-- variants -->
              <SidebarItem v-if="variantPermission.view || variantPermission.create" title="Varyantlar"
                icon="fas fa-cube" uri="/app/catalog/variants" />
              <!-- variant_values -->
              <SidebarItem v-if="attributeValuePermission.view || attributeValuePermission.create"
                title="Varyant Değerleri" icon="fas fa-cube" uri="/app/catalog/variant_values" />
            </SidebarSubListItem>
          </SidebarSubListItem>
          <!-- /catalog -->
          <!-- customers -->
          <!-- Customers one item -->
          <SidebarItem v-if="wizardCompleted" title="Müşteriler" icon="fas fa-address-book" uri="/app/customers" />
          <!-- users -->
          <SidebarSubListItem v-if="wizardCompleted" title="Kullanıcılar" icon="fas fa-users">
            <!-- users -->
            <SidebarItem v-if="userPermission.view || userPermission.create" title="Kullanıcılar" icon="fas fa-users"
              uri="/app/users" />
            <!-- roles -->
            <SidebarItem v-if="permissionPermission.view || permissionPermission.create" title="Roller"
              icon="fas fa-user-tag" uri="/app/permissions" />
          </SidebarSubListItem>
          <!-- /users -->
          <!-- firm info -->
          <SidebarSubListItem title="Ayarlar" icon="fa fa-cog">
            <SidebarItem v-if="customerPermission.update" title="Hesabım" icon="fas fa-cogs" uri="/app/profile" />
            <SidebarItem v-if="paymentPermission.view" title="Ödemeler" icon="fas fa-file-invoice"
              uri="/app/payments" />
          </SidebarSubListItem>
          <!-- bank_accounts -->
          <SidebarSubListItem v-if="wizardCompleted && planData?.simple_accounting" title="Finans"
            icon="fas fa-university">
            <SidebarItem title="Hareketler" icon="fas fa-exchange-alt" uri="/app/transactions" />
            <SidebarItem v-if="(customerBankAccountPermission.view ||
              customerBankAccountPermission.create)" title="Banka Hesapları" icon="fas fa-university"
              uri="/app/bank_accounts" />
          </SidebarSubListItem>

          <SidebarItem v-if="wizardCompleted" title="Abonelik" icon="fas fa-cogs" uri="/app/plans" />

        </SidebarMenuWrapper>
        <!-- /layout -->
      </ul>
    </div>
    <!-- /main navigation -->
  </div>
</template>

<style lang="scss" scoped></style>
