<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { list } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useOfferFormStore } from '/@src/stores/offerFormStore';
const router = useRouter();
const swal = useSwal();
const isLoading = ref<boolean>(false);
const store = useOfferFormStore();
const customers = ref<Array<{
    id: string
    name: string
}>>([])

const getPageQueries = () => router.currentRoute.value.query;

const getCustomers = async () => {
    isLoading.value = true;
    const { data } = await list();
    customers.value = data.data;

    let query = getPageQueries();
    if (query.customer_id) {
        //check if customer_id is in the list set it as selected
        let customer = customers.value.find((customer) => customer.id == query.customer_id);
        if (customer) {
            store.offerForm.detail.company_customer_id = customer.id;
        } else {
            store.offerForm.detail.company_customer_id = '';
            swal.fire({
                title: 'Müşteri Bulunamadı',
                text: 'Müşteri bulunamadı. Müşteri listesinden seçim yapınız.',
                icon: 'warning',
                confirmButtonText: 'Tamam',
            })
        }
    } else {
        if (!store.offerForm.detail.company_customer_id) {
            store.offerForm.detail.company_customer_id = '';
        }
    }
    isLoading.value = false;

}

onMounted(async () => {
    await getCustomers();
})

</script>
<template>
    <div class="offer-form-customer-wrapper card">
        <VLoader :active="isLoading" />
        <!-- offer_date -->
        <div class="card-body">
            <!-- customer -->
            <InputWithError :errors="store.formErrors.detail.company_customer_id">
                <label>Müşteri<i class="fa fa-sync-alt ml-2 cursor-pointer" @click="getCustomers"
                        v-if="!store.offerRequestMode"></i></label>
                <select v-model="store.offerForm.detail.company_customer_id" class="form-control"
                    :disabled="store.offerRequestMode">
                    <option value="">Seçiniz</option>
                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                        {{ customer.name }}
                    </option>
                </select>
            </InputWithError>
            <div class="row">
                <div class="col-md-12">
                    <OfferFormCustomerDetail />
                </div>
            </div>
        </div>
    </div>
</template>
