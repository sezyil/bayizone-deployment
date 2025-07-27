<script setup lang="ts">
import { ICheckoutCustomerResponse } from '/@src/request/request-plans';
import { usePaymentStore,IPaymentStoreInvoiceDetail } from '/@src/stores/paymentStore';
const paymentStore = usePaymentStore();
const storeData = computed(() => paymentStore.orderData);
const modalActive = ref(false);

const formData = ref<IPaymentStoreInvoiceDetail>({
    invoice_address: '',
    invoice_city: '',
    invoice_country: '',
    invoice_email: '',
    invoice_firm_name: '',
    invoice_phone: '',
    invoice_state: '',
    invoice_tax_administration: '',
    invoice_tax_no: '',
    invoice_country_id: 0,
    invoice_state_id: 0,
    invoice_city_id: 0,
    invoice_postcode: '',
});

const onSubmit = (e: Event) => {
    e.preventDefault();
    paymentStore.setInvoice(formData.value);
};

onMounted(() => {
    if (storeData.value) {
        const { invoice_address, invoice_city, invoice_country, invoice_email, invoice_firm_name, invoice_phone, invoice_state, invoice_tax_administration, invoice_tax_no, invoice_country_id, invoice_state_id, invoice_city_id } = storeData.value;
        formData.value = {
            invoice_address,
            invoice_city,
            invoice_country,
            invoice_email,
            invoice_firm_name,
            invoice_phone,
            invoice_state,
            invoice_tax_administration,
            invoice_tax_no,
            invoice_country_id,
            invoice_state_id,
            invoice_city_id,
            invoice_postcode: '',
        };
    }
});
</script>

<template>
    <div class="mb-4 mb-lg-2" v-if="storeData">
        <span class="text-muted">Fatura Detayları:
            <i class="icon-pencil ml-2 cursor-pointer" @click="modalActive = true"></i>
        </span>


        <ul class="list list-unstyled mb-0">
            <li>
                <h5 class="my-2">{{ storeData?.invoice_firm_name }}</h5>
            </li>
            <li><strong>Adres:</strong>
                {{ storeData?.invoice_address }}
            </li>
            <li>{{ storeData?.invoice_city }}/{{ storeData?.invoice_state }}/{{ storeData?.invoice_country }}</li>
            <li><strong>V.No/Daire:</strong>{{ storeData?.invoice_tax_no }}/{{ storeData?.invoice_tax_administration }}</li>
            <li><strong>Tel:</strong>{{ storeData?.invoice_phone }}</li>
            <li><strong>Mail:</strong>{{ storeData?.invoice_email }}</li>
        </ul>
        <VModal :show="modalActive" @close="modalActive = false" title="Fatura Bilgileri">
            <form @submit.prevent="onSubmit">
                <div class="form-group">
                    <label>Firma Adı</label>
                    <input type="text" class="form-control" v-model="formData.invoice_firm_name">
                </div>
                <div class="form-group">
                    <label>Adres</label>
                    <input type="text" class="form-control" v-model="formData.invoice_address">
                </div>
                <div class="form-group">
                    <VInputCountries v-model="formData.invoice_country_id" @update:country_name="formData.invoice_country = $event"
                        :withName="true" />
                </div>
                <div class="form-group">
                    <VInputStates v-model="formData.invoice_state_id" :country_id="formData?.invoice_country_id"
                        @update:state_name="formData.invoice_state = $event" :withName="true" />
                </div>
                <div class="form-group">
                    <VInputCities v-model="formData.invoice_city_id" :state_id="formData?.invoice_state_id"
                        @update:city_name="formData.invoice_city = $event" :withName="true" />
                </div>
                <div class="form-group">
                    <label>Vergi No</label>
                    <input type="text" class="form-control" v-model="formData.invoice_tax_no">
                </div>
                <div class="form-group">
                    <label>Vergi Dairesi</label>
                    <input type="text" class="form-control" v-model="formData.invoice_tax_administration">
                </div>
                <div class="form-group">
                    <label>Telefon</label>
                    <input type="text" class="form-control" v-model="formData.invoice_phone">
                </div>
                <div class="form-group">
                    <label>E-Mail</label>
                    <input type="text" class="form-control" v-model="formData.invoice_email">
                </div>
            </form>
            <template #footer>
                <button type="button" class="btn btn-bayi-red" @click.prevent="onSubmit">Kaydet</button>
            </template>
        </VModal>
    </div>

</template>


<style scoped></style>