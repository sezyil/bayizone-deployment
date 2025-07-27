<script setup lang="ts">
import { useOfferFormStore } from '/@src/stores/offerFormStore';
const offer_date_el = ref(null);
const offer_due_date_el = ref(null);


const store = useOfferFormStore();
</script>
<template>
    <div class="offer-form-customer-wrapper card">
        <div class="card-body">

            <!-- offer_no -->
            <InputWithError :errors="store.formErrors.detail.offer_no">
                <label>Teklif No</label>
                <input type="text" v-model="store.offerForm.detail.offer_no" disabled class="form-control disabled" />
            </InputWithError>

            <div class="row">
                <div class="col-md-6">
                    <!-- offer_date -->
                    <InputWithError :errors="store.formErrors.detail.offer_date">
                        <label>Teklif Tarihi <span class="text-danger">*</span></label>
                        <input type="date" v-model="store.offerForm.detail.offer_date" class="form-control"
                            ref="offer_date_el" />
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <!-- offer_due_date -->
                    <InputWithError :errors="store.formErrors.detail.offer_due_date">
                        <label>Teklif Geçerlilik Tarihi <span class="text-danger">*</span></label>
                        <input type="date" v-model="store.offerForm.detail.offer_due_date" class="form-control"
                            :min="store.offerForm.detail.offer_date.toString()" ref="offer_due_date_el" />
                    </InputWithError>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- is_international checkbox -->
                    <InputWithError :errors="store.formErrors.detail.is_international" class="input-international-wrapper">
                        <div class="custom-control custom-control-right custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="input_international"
                                v-model="store.offerForm.detail.is_international">
                            <label class="custom-control-label" for="input_international">Yurt Dışı</label>
                        </div>
                        <small class="form-text text-danger">Yurt dışı işlemlerinde KDV uygulanmaz.</small>
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <!-- delivery_type text input -->
                    <InputWithError :errors="store.formErrors.detail.delivery_type">
                        <label>Teslimat Şekli</label>
                        <input type="text" v-model="store.offerForm.detail.delivery_type" class="form-control" />
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <!-- payment_type text input -->
                    <InputWithError :errors="store.formErrors.detail.payment_type">
                        <label>Ödeme Şekli</label>
                        <input type="text" v-model="store.offerForm.detail.payment_type" class="form-control" />
                    </InputWithError>
                </div>
                <!-- incoterm -->
                <!-- <div class="col-md-12">
                    <InputWithError :errors="store.formErrors.detail.incoterms">
                        <label>Incoterm</label>
                        <textarea v-model="store.offerForm.detail.incoterms" class="form-control"></textarea>
                    </InputWithError>
                </div> -->
                <div class="col-md-12">
                    <label>Sipariş Notu</label>
                    <!-- note -->
                    <InputWithError :errors="store.formErrors.detail.note">
                        <textarea v-model="store.offerForm.detail.note" class="form-control"></textarea>
                    </InputWithError>
                </div>
                <div class="col-md-12" v-if="store.offerForm.detail.dealer_note">
                    <label>Müşteri Notu</label>
                    <!-- note -->
                    <textarea :value="store.offerForm.detail.dealer_note" readonly class="form-control disabled"></textarea>
                    <small class="form-text text-warning">Teklif isteğinden gelen not.</small>
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
