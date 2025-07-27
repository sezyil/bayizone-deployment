<script  setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle('Müşteri Detayı');

const { id: company_customer_id } = route.params as { id: string };
const permission = useUserPermission().getByName('company_customer');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}

//if param id not string or empty redirect to user page
if (!company_customer_id) router.push('/app/customers');
</script>

<template>
    <PageContent>
        <template #header>
            <RouterLink class="btn btn-bayi-red" :to="'/app/customers/' + company_customer_id">
                <i class="fas fa-arrow-left"></i> Müşteri Detayı
            </RouterLink>
        </template>
        <CustomerDetailForm :id="company_customer_id" v-if="permission.update" />
    </PageContent>
</template>

<style lang="scss" scoped></style>