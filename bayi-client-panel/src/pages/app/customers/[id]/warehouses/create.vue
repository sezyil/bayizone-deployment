<script setup lang="ts">
import { swalPermissionDenied } from "/@src/composables/useSwal";
import { useUserPermission } from "/@src/composables/useUserPermission";
import { useViewWrapper } from "/@src/stores/viewWrapper";

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle("Müşteri Depo Oluştur");
const permission = useUserPermission().getByName("company_customer_warehouse");
if (!permission.create) {
  await swalPermissionDenied(() => router.push("/app"));
}
const { id: company_customer_id } = route.params as { id: string };
if (!company_customer_id) router.push("/app/customers");
</script>

<template>
  <PageContent>
    <CustomerWarehouseForm :customer_id="company_customer_id" v-if="permission.create" />
  </PageContent>
</template>

<style lang="scss" scoped></style>
