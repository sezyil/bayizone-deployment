<script setup lang="ts">
import { swalPermissionDenied } from "/@src/composables/useSwal";
import { useUserPermission } from "/@src/composables/useUserPermission";
import { useViewWrapper } from "/@src/stores/viewWrapper";

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle("Müşteri Banka Hesabı Oluştur");
const permission = useUserPermission().getByName("company_customer_bank_account");
if (!permission.create) {
  await swalPermissionDenied(() => router.push("/app"));
}
const { id: company_customer_id } = route.params as { id: string };
if (!company_customer_id) router.push("/app/customers");
</script>

<template>
  <PageContent>
    <CustomerBankAccountForm
      v-if="permission.create"
      :customer_id="company_customer_id"
    />
  </PageContent>
</template>

<style lang="scss" scoped></style>
