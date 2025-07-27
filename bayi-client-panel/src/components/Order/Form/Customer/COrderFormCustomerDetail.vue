<script setup lang="ts">
import { get } from '/@src/request/request-customer';
import { ICompanyCustomerForm } from '/@src/shared/form/interface-company-customer';
import { useCustomerOrderFormStore } from '/@src/stores/customerOrderFormStore';
const isLoading = ref<boolean>(false);
const store = useCustomerOrderFormStore();
const customerData = computed(() => store.getCustomerInfo);

const getCustomer = async () => {
    isLoading.value = true;
    let customer_id = store.orderForm.detail.company_customer_id;
    const { data } = await get(customer_id);
    store.setCustomerInfo(data.data as ICompanyCustomerForm);

    isLoading.value = false;
}
watch(() => store.orderForm.detail.company_customer_id, async () => {
    let customer = store.orderForm.detail.company_customer_id;
    if (!customer) {
        store.setCustomerInfo(null);
        return;
    }
    await getCustomer();
})

onMounted(() => {
    if (store.orderForm.detail.company_customer_id) {
        getCustomer();
    }
})

</script>
<template>
    <VLoader :active="isLoading" />
    <div class="card-body customer-detail-card mb-3" v-if="customerData">
        <div class="d-sm-flex flex-sm-wrap mb-3">
            <div class="font-weight-semibold">Firma Adı:</div>
            <div class="ml-sm-auto mt-2 mt-sm-0">{{ customerData?.company_name }}</div>
        </div>

        <div class="d-sm-flex flex-sm-wrap mb-3">
            <!-- yetkili -->
            <div class="font-weight-semibold">Yetkili:</div>
            <div class="ml-sm-auto mt-2 mt-sm-0">{{ customerData?.authorized_name }}
            </div>
        </div>

        <div class="d-sm-flex flex-sm-wrap mb-3">
            <div class="font-weight-semibold">Telefon:</div>
            <div class="ml-sm-auto mt-2 mt-sm-0">{{ customerData?.phone }}</div>
        </div>

        <div class="d-sm-flex flex-sm-wrap">
            <div class="font-weight-semibold">E-posta:</div>
            <div class="ml-sm-auto mt-2 mt-sm-0">{{ customerData?.email }}</div>
        </div>
    </div>
</template>
<style scoped>
.customer-detail-card {
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    padding: 1rem;
}
</style>