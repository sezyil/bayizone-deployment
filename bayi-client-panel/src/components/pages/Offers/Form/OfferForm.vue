<script setup lang="ts">
import { useApi } from '/@src/composables/useApi';
import { useSwal } from '/@src/composables/useSwal';
import CustomerOfferApi from '/@src/request/request-customer-offer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { OfferRequestFormResponse } from '/@src/shared/response/interface-offer-request-response';
import { useOfferFormStore } from '/@src/stores/offerFormStore';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = useSwal();
const store = useOfferFormStore();
const api = new CustomerOfferApi();
const __api = useApi()
const props = defineProps({
    offer_id: {
        type: String,
    },
    requestId: {
        type: String,
        required: false
    }
})
const offerRequestData = ref<OfferRequestFormResponse>()
const getData = async () => {
    if (props.offer_id) {
        try {
            const { data } = await api.get(props?.offer_id);
            store.setFormData(data.data);

        } catch (error: any) {
            catchFieldError(error, undefined, '/app/offers');
        }
    } else if (props.requestId) {
        try {
            const { data } = await __api.get(`/offer_requests/${props.requestId}/edit`)
            let __data = data.data as OfferRequestFormResponse
            offerRequestData.value = __data
            store.offerForm.detail.company_customer_id = __data.offer.company_customer_id
            store.offerForm.detail.dealer_note = __data.offer.global_note
            store.offerForm.detail.currency = __data.offer.currency
        } catch (error: any) {
            catchFieldError(error, undefined, '/app/offer_requests');
        }
    }


}

const hasCustomerInfo = computed(() => store.getCustomerInfo !== null)

onMounted(async () => {
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
                <OfferFormDetail />
            </div>
            <div class="col-12 col-xl-6">
                <OfferFormCustomer />
            </div>
        </div>
        <div v-if="hasCustomerInfo">
            <div class="row">
                <div class="col-md-12">
                    <OfferFormProductWrapper :request-products="offerRequestData?.offer.lines"
                        :request-currency="offerRequestData?.offer.currency" />

                </div>
            </div>
            <div class="row">
                <div class="col-12 col-xl-6">
                    <OfferFormInvoice />
                </div>
                <div class="col-12 col-xl-6">
                    <OfferFormBankAccount />
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