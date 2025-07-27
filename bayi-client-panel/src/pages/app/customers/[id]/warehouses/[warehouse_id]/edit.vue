<script  setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle('Müşteri Depo Düzenle');
const permission = useUserPermission().getByName('company_customer_warehouse');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
const { id: company_customer_id, warehouse_id } = route.params as { id: string, warehouse_id: string };

//if param id not string or empty redirect to user page
if (!company_customer_id || !warehouse_id) router.push('/app/customers/warehouses');
</script>

<template>
    <PageContent>
        <CustomerWarehouseForm :customer_id="company_customer_id" :warehouse_id="warehouse_id" v-if="permission.update" />
    </PageContent>
</template>

<style lang="scss" scoped></style>