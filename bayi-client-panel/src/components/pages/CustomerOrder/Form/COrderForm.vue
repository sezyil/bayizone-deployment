<script setup lang="ts">
import { useApi } from '/@src/composables/useApi';
import { useSwal } from '/@src/composables/useSwal';
import CustomerOrderApi from '/@src/request/request-customer-order';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useCustomerOrderFormStore } from '/@src/stores/customerOrderFormStore';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = useSwal();
const store = useCustomerOrderFormStore();
const api = new CustomerOrderApi();
const __api = useApi()
const props = defineProps({
    order_id: {
        type: String,
    },
    previewMode: {
        type: Boolean,
        default: false
    }
})
const getData = async () => {
    if (!props.order_id) return;
    try {
        const { data } = await api.get(props.order_id);
        store.setFormData(data.data);
        store.setPreviewMode(props.previewMode);

    } catch (error: any) {
        catchFieldError(error, undefined, '/app/customer_orders');
    }
}

const hasCustomerInfo = computed(() => store.getCustomerInfo !== null)

onMounted(async () => {
    if (props.order_id)
        await getData();
})


onBeforeUnmount(() => {
    store.$reset();
    store.$dispose();
})

</script>
<template>
    <div class="offer-form-wrapper">
        <div class="row">
            <div class="col-12 col-xl-6">
                <COrderFormDetail />
            </div>
            <div class="col-12 col-xl-6">
                <COrderFormCustomer />
            </div>
        </div>
        <div v-if="hasCustomerInfo">
            <div class="row">
                <div class="col-md-12">
                    <OrderFormProductWrapper />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <COrderFormInvoice />
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Form müşteri seçildiğinde görüntülenecektir.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>