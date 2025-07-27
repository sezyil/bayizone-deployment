<script setup lang="ts">
import { useCustomerOrderFormStore } from '/@src/stores/customerOrderFormStore';
const offer_date_el = ref(null);
const offer_due_date_el = ref(null);


const store = useCustomerOrderFormStore();
</script>
<template>
    <div class="offer-form-customer-wrapper card">
        <div class="card-body">
            <div class="row">
                <!-- offer_no -->
                <div class="col-12 col-md-6">
                    <InputWithError :errors="store.formErrors.detail.order_no">
                        <label>Sipariş No</label>
                        <input type="text" v-model="store.orderForm.detail.order_no" class="form-control" disabled />
                    </InputWithError>
                </div>
                <div class="col-12 col-md-6">
                    <!-- offer_date -->
                    <InputWithError :errors="store.formErrors.detail.order_date">
                        <label>Sipariş Tarihi</label>
                        <input type="date" v-model="store.orderForm.detail.order_date" class="form-control"
                            :readonly="store.previewMode" ref="offer_date_el" />
                    </InputWithError>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <InputWithError :errors="store.formErrors.detail.is_international"
                        class="input-international-wrapper">
                        <div class="custom-control custom-control-right custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="input_international"
                                :disabled="store.previewMode" v-model="store.orderForm.detail.is_international">
                            <label class="custom-control-label" for="input_international">Yurt Dışı</label>
                        </div>
                        <small class="form-text text-danger">Yurt dışı işlemlerinde KDV uygulanmaz.</small>
                    </InputWithError>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <!-- delivery_type text input -->
                    <InputWithError :errors="store.formErrors.detail.currency">
                        <VInputCurrencies v-model="store.orderForm.detail.currency" />
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <!-- payment_type text input -->
                    <InputWithError :errors="store.formErrors.detail.payment_type">
                        <label>Ödeme Şekli</label>
                        <input type="text" v-model="store.orderForm.detail.payment_type" class="form-control" />
                    </InputWithError>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <InputWithError :errors="store.formErrors.detail.delivery_type">
                        <label>Teslimat Şekli</label>
                        <input type="text" v-model="store.orderForm.detail.delivery_type" class="form-control" />
                    </InputWithError>
                </div>
                <!-- incoterm -->
                <!-- <div class="col-md-12">
                    <InputWithError :errors="store.formErrors.detail.incoterms">
                        <label>Incoterm</label>
                        <textarea v-model="store.orderForm.detail.incoterms" class="form-control"></textarea>
                    </InputWithError>
                </div> -->

                <div class="col-md-12">
                    <label>Sipariş Notu</label>
                    <!-- note -->
                    <InputWithError :errors="store.formErrors.detail.note">
                        <textarea v-model="store.orderForm.detail.note" class="form-control"
                            :disabled="store.previewMode"></textarea>
                    </InputWithError>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
.input-international-wrapper {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
}
</style>
