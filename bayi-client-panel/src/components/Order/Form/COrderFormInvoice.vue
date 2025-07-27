<script setup lang="ts">
import { ECompanyCustomerType } from '/@src/shared/form/interface-company-customer';
import { useCustomerOrderFormStore } from '/@src/stores/customerOrderFormStore';
const store = useCustomerOrderFormStore()
const customerInfo = computed(() => store.getCustomerInfo)
const isFirm = computed(() => customerInfo.value?.type === ECompanyCustomerType.Company)
const taxLabel = computed(() => isFirm.value ? 'Vergi Numarası' : 'TC Kimlik Numarası')


</script>
<template>
  <div class="invoice-wrapper card">
    <!-- billing_country_id -->
    <div class="card-header">
      <div class="card-title">
        <h5>Fatura Bilgileri</h5>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <!-- billing_company_name -->
          <InputWithError>
            <label>Firma Adı</label>
            <input type="text" :value="customerInfo?.company_name" class="form-control" disabled />
          </InputWithError>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6" v-if="isFirm">
          <!-- billing_tax_office -->
          <InputWithError>
            <label>Vergi Dairesi</label>
            <input type="text" :value="customerInfo?.tax_office" class="form-control" disabled />
          </InputWithError>
        </div>
        <div class="col-md-6">
          <!-- billing_tax_number -->
          <InputWithError>
            <label>{{ taxLabel }}</label>
            <input type="text" :value="customerInfo?.tax_identity_no" class="form-control" disabled />
          </InputWithError>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <!-- billing_city_id -->
          <VInputCountries v-model="store.orderForm.detail.billing_country_id"
            :errors="store.formErrors.detail.billing_country_id" :readonly="store.previewMode" />
        </div>
        <div class="col-md-4">
          <!-- billing_state_id -->
          <VInputStates v-model="store.orderForm.detail.billing_state_id"
            :country_id="store.orderForm.detail.billing_country_id" :errors="store.formErrors.detail.billing_state_id" :readonly="store.previewMode" />
        </div>
        <div class="col-md-4">
          <!-- billing_city_id -->
          <VInputCities v-model="store.orderForm.detail.billing_city_id"
            :state_id="store.orderForm.detail.billing_state_id" :errors="store.formErrors.detail.billing_city_id" :readonly="store.previewMode" />
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <!-- billing_address -->
          <InputWithError :errors="store.formErrors.detail.billing_address">
            <label>Adres</label>
            <input type="text" v-model="store.orderForm.detail.billing_address" class="form-control" :disabled="store.previewMode" />
          </InputWithError>
        </div>
      </div>
    </div>
  </div>
</template>