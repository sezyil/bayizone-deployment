<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import PaymentApi, { IRequestPayDataResponse } from '/@src/request/request-payment';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = useSwal();
//get the plan id from the route
const route = useRoute();
const router = useRouter();
const { id } = route.params as { id: string };
//get success query
const { success } = route.query as { success: string };

const viewWrapper = useViewWrapper();
const api = new PaymentApi();
viewWrapper.setPageTitle('Ödemeler');
const permission = useUserPermission().getByName('payment');
onBeforeMount(async () => {
    if (!permission.view) {
        await swalPermissionDenied(() => router.push('/app'));
    }
});
const orderData = ref<IRequestPayDataResponse>();
const getPlanData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.show(id);
        const xData = data.data as IRequestPayDataResponse;
        orderData.value = xData;
    } catch (error) {
        catchFieldError(error);
    } finally {
        viewWrapper.setLoading(false);
    }
};

onMounted(async () => {
    if (!id) {
        router.push('/app/payments');
    }

    if (success) {
        if (success === '1') {
            swal.fire({
                title: 'Ödeme İşlemi',
                html: 'Ödeme işlemi başarılı bir şekilde gerçekleşti. Teşekkür ederiz. Hizmetlerinizi kullanmaya başlayabilirsiniz.',
                icon: 'success',
            });
        }
    }

    await getPlanData();
});
</script>

<template>
    <PageContent>
        <PaymentShow v-if="orderData" :storeData="orderData" />
    </PageContent>
</template>

<style scoped></style>