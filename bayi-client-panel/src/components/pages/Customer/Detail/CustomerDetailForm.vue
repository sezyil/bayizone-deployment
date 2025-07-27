<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { update, add, get } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { ECompanyCustomerType, ICompanyCustomerForm, ICompanyCustomerFormErrors, getCompanyCustomerGroupDescription } from '/@src/shared/form/interface-company-customer';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { resetFormData, resetFormErrors } from '/@src/utils/customer_form';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const props = defineProps({
    id: {
        type: String,
    }
})
const formData = ref<ICompanyCustomerForm>(resetFormData())
const customerType = computed(() => formData.value.type)
const taxLabel = computed(() => customerType.value === ECompanyCustomerType.Company ? 'Vergi/Vat No' : 'TC Kimlik No')
const labelName = computed(() => customerType.value === ECompanyCustomerType.Company ? 'Yetkili Adı' : 'Adı')

const formErrors = ref<ICompanyCustomerFormErrors>(resetFormErrors());

const _resetFormErrors = () => formErrors.value = resetFormErrors()

const sendFormData = async () => {
    viewWrapper.setLoading(true)
    _resetFormErrors()
    let _txt = props.id ? 'güncellendi' : 'eklendi.'
    try {
        if (props.id) {
            await update(formData.value, props.id)
        } else {
            await add(formData.value)
        }
        swal.fire({
            icon: 'success',
            title: 'Başarılı',
            text: 'Müşteri bilgileri başarıyla ' + _txt
        }).then(() => {
            router.push('/app/customers')
        })

    } catch (error) {
        catchFieldError(error, (field: keyof ICompanyCustomerForm, message: string) => {
            formErrors.value[field] = [message]
        })
    }
    viewWrapper.setLoading(false)
}

const getData = async () => {
    if (!props.id) return
    viewWrapper.setLoading(true)
    try {
        const { data: response } = await get(props.id)
        formData.value = response.data
        viewWrapper.setPageTitle(`${formData.value.company_name} | Müşteri Detayı`)
    } catch (error) {
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Müşteri bilgileri alınırken bir hata oluştu'
        }).then(() => {
            router.push('/app/customers')
        })
    }
    viewWrapper.setLoading(false)
}

onMounted(() => {
    if (props.id) getData()
})

</script>
<template>
    <div class="card">

        <div class="card-header">
            <h6 class="card-title">Müşteri Bilgileri</h6>
        </div>

        <div class="card-body">
            <form @submit.prevent="sendFormData()" autocomplete="off">
                <!-- Müşteri Grubu 'POTENTIAL' | 'CUSTOMER' | 'SUPPLIER'; -->
                <div class="row">
                    <!-- btn group -->
                    <div class="col-md-12 d-flex flex-row align-items-center">
                        <label for="customerGroup">Müşteri Grubu</label>
                        <InputWithError :errors="formErrors.group" class="mb-0 ml-3">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-outline-secondary"
                                    :class="{ active: formData.group === 'POTENTIAL' }">
                                    <input type="radio" v-model="formData.group" value="POTENTIAL">
                                    <i class="fas fa-user-plus mr-1 ml-1"></i> {{
                                    getCompanyCustomerGroupDescription('POTENTIAL') }}
                                </label>
                                <label class="btn btn-outline-secondary"
                                    :class="{ active: formData.group === 'CUSTOMER' }">
                                    <input type="radio" v-model="formData.group" value="CUSTOMER">
                                    <i class="fas fa-user mr-1 ml-1"></i> {{
                                    getCompanyCustomerGroupDescription('CUSTOMER') }}
                                </label>
                                <label class="btn btn-outline-secondary"
                                    :class="{ active: formData.group === 'SUPPLIER' }">
                                    <input type="radio" v-model="formData.group" value="SUPPLIER">
                                    <i class="fas fa-user-tie mr-1 ml-1"></i> {{
                                    getCompanyCustomerGroupDescription('SUPPLIER') }}
                                </label>
                            </div>
                        </InputWithError>
                    </div>
                </div>
                <!-- Müşteri Tipi 1 tüzel 2 gerçek -->
                <div class="row">
                    <div class="col-md-12 d-flex flex-row align-items-center">
                        <label for="customerType">Müşteri Tipi</label>
                        <InputWithError :errors="formErrors.type" class="mb-0 ml-3">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-outline-secondary"
                                    :class="{ active: formData.type === ECompanyCustomerType.Company }">
                                    <input type="radio" v-model="formData.type" :value="ECompanyCustomerType.Company">
                                    <i class="fas fa-building mr-1 ml-1"></i> Tüzel
                                </label>
                                <label class="btn btn-outline-secondary"
                                    :class="{ active: formData.type === ECompanyCustomerType.Person }">
                                    <input type="radio" v-model="formData.type" :value="ECompanyCustomerType.Person">
                                    <i class="fas fa-user mr-1 ml-1"></i> Gerçek
                                </label>
                            </div>

                        </InputWithError>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Firma Adı -->
                        <InputWithError :errors="formErrors.company_name">
                            <label>Firma Adı</label>
                            <input type="text" v-model="formData.company_name" class="form-control"
                                placeholder="Firma Adı">
                        </InputWithError>
                    </div>
                </div>

                <div class="row">
                    <!-- Yetkili adı ve soyadı col-lg-6 -->
                    <div class="col-lg-12">
                        <!-- Yetkili Kişi ADI -->
                        <InputWithError :errors="formErrors.authorized_name">
                            <label>{{ labelName }}</label>
                            <input type="text" v-model="formData.authorized_name" class="form-control"
                                :placeholder="labelName">
                        </InputWithError>
                    </div>
                </div>

                <div class="row">
                    <div :class="customerType === ECompanyCustomerType.Company ? 'col-lg-6' : 'col-lg-12'">
                        <!-- Vergi No -->
                        <InputWithError :errors="formErrors.tax_identity_no">
                            <label>{{ taxLabel }}</label>
                            <input type="text" v-model="formData.tax_identity_no" class="form-control" minlength="10"
                                maxlength="11" :placeholder="taxLabel">
                        </InputWithError>
                    </div>
                    <div v-if="customerType === ECompanyCustomerType.Company" class="col-lg-6">
                        <!-- Vergi Dairesi -->
                        <InputWithError :errors="formErrors.tax_office">
                            <label>Vergi Dairesi</label>
                            <input type="text" v-model="formData.tax_office" class="form-control"
                                placeholder="Vergi Dairesi">
                        </InputWithError>
                    </div>
                </div>

                <!-- Telefon fax col 6 -->
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- Email -->
                        <InputWithError :errors="formErrors.email">
                            <label>Email</label>
                            <input type="email" v-model="formData.email" class="form-control" placeholder="Email">
                        </InputWithError>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- Telefon -->
                        <InputWithError :errors="formErrors.phone">
                            <label>Telefon</label>
                            <input type="text" v-model="formData.phone" class="form-control" placeholder="Telefon">
                        </InputWithError>
                    </div>
                </div>

                <div class="row">
                    <!-- Ülke -->
                    <div class="col-lg-12">
                        <VInputCountries v-model="formData.country_id" :errors="formErrors.country_id" />
                    </div>
                    <div class="col-lg-6">
                        <!-- İl -->
                        <VInputStates v-model="formData.state_id" :country_id="formData.country_id"
                            :errors="formErrors.state_id" />
                    </div>
                    <div class="col-lg-6">
                        <!-- İlçe -->
                        <VInputCities v-model="formData.city_id" :state_id="formData.state_id"
                            :errors="formErrors.city_id" />
                    </div>
                </div>

                <!-- Adres -->
                <InputWithError :errors="formErrors.address">
                    <label>Adres</label>
                    <textarea v-model="formData.address" class="form-control" placeholder="Adres"></textarea>
                </InputWithError>

                <!-- Posta Kodu -->
                <InputWithError :errors="formErrors.postcode">
                    <label>Posta Kodu</label>
                    <input type="text" v-model="formData.postcode" class="form-control" placeholder="Posta Kodu">
                </InputWithError>

                <!-- Language -->
                <InputWithError :errors="formErrors.language">
                    <label>Müşteri Dili</label>
                    <select v-model="formData.language" class="form-control">
                        <option value="tr">Türkçe</option>
                        <option value="en">İngilizce</option>
                    </select>
                </InputWithError>

                <!-- Status -->
                <InputWithError :errors="formErrors.status">
                    <label>Durum</label>
                    <select v-model="formData.status" class="form-control">
                        <option :value="true">Aktif</option>
                        <option :value="false">Pasif</option>
                    </select>
                </InputWithError>

                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>