<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { get, update } from '/@src/request/request-company'
import { SwalInstance } from '/@src/shared/common/type-swal';
import { IProfileCompanyForm, IProfileCompanyFormErrors } from '/@src/shared/form/interface-profile-company';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const isLoading = ref<boolean>(false);
const swal = useSwal();
const formData = ref<IProfileCompanyForm>({
    authorized_person: "",
    firm_name: '',
    tax_no: '',
    tax_administration: "",
    address: '',
    postcode: "",
    country_id: 0,
    state_id: 0,
    city_id: 0,
    email: "",
    phone: "",
    fax: "",
});

const formErrors = ref<IProfileCompanyFormErrors>({
    firm_name: [],
    tax_no: [],
    tax_administration: [],
    address: [],
    postcode: [],
    city_id: [],
    state_id: [],
    country_id: [],
    email: [],
    phone: [],
    fax: [],
    authorized_person: [],
});

const resetErrors = () => {
    formErrors.value = {
        firm_name: [],
        tax_no: [],
        tax_administration: [],
        address: [],
        postcode: [],
        city_id: [],
        state_id: [],
        country_id: [],
        email: [],
        phone: [],
        fax: [],
        authorized_person: [],
    }
}

type ProfileCompanyFormType = keyof IProfileCompanyForm;

const sendForm = async () => {
    isLoading.value = true
    resetErrors()
    try {
        const { data } = await update(formData.value)
        let _data = data.data as {
            trial_activated: boolean;
        }

        let _msg = 'Bilgileriniz güncellendi';
        if (_data.trial_activated) {
            _msg = 'Bilgileriniz güncellendi. 7 Günlük deneme süreniz başladı.';
        }
        //process is success then show success message
        swal.fire({
            icon: 'success',
            title: 'Başarılı',
            text: _msg
        }).then(() => {
            location.reload()
        })

    } catch (error) {
        catchFieldError(error, (field: ProfileCompanyFormType, errors: any) => {
            formErrors.value[field].push(errors);
        })
    }
    isLoading.value = false
}


const getData = async () => {
    isLoading.value = true
    try {
        const { data } = await get()
        formData.value = data.data as IProfileCompanyForm
    } catch (error) {
        catchFieldError(error)
    }
    isLoading.value = false
}

onMounted(() => {
    getData()
});



</script>
<template>
    <div class="card">

        <div class="card-header">
            <h6 class="card-title">Firma Bilgileri</h6>
        </div>

        <div class="card-body">
            <VLoader :active="isLoading" />
            <form @submit.prevent="sendForm" autocomplete="off">
                <!-- Firma Ünvanı -->
                <InputWithError :errors="formErrors.firm_name">
                    <label>Firma Ünvanı <span class="text-danger">*</span></label>
                    <input type="text" v-model="formData.firm_name" class="form-control">
                </InputWithError>

                <div class="row">
                    <div class="col-lg-6">
                        <!-- Vergi No -->
                        <InputWithError :errors="formErrors.tax_no">
                            <label>Vergi No <span class="text-danger">*</span></label>
                            <input type="text" v-model="formData.tax_no" class="form-control" minlength="10"
                                maxlength="11">
                        </InputWithError>
                    </div>
                    <div class="col-lg-6">
                        <!-- Vergi Dairesi -->
                        <InputWithError :errors="formErrors.tax_administration">
                            <label>Vergi Dairesi <span class="text-danger">*</span></label>
                            <input type="text" v-model="formData.tax_administration" class="form-control">
                        </InputWithError>
                    </div>
                </div>


                <!-- Yetkili Kişi -->
                <InputWithError :errors="formErrors.authorized_person">
                    <label>Yetkili Kişi <span class="text-danger">*</span></label>
                    <input type="text" v-model="formData.authorized_person" class="form-control">
                </InputWithError>

                <!-- Telefon -->
                <InputWithError :errors="formErrors.phone">
                    <label>Telefon <span class="text-danger">*</span></label>
                    <input type="text" v-model="formData.phone" class="form-control">
                </InputWithError>

                <!-- Email -->
                <InputWithError :errors="formErrors.email">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" v-model="formData.email" class="form-control" disabled />
                </InputWithError>


                <!-- Adres -->
                <InputWithError :errors="formErrors.address">
                    <label>Adres <span class="text-danger">*</span></label>
                    <textarea v-model="formData.address" class="form-control"></textarea>
                </InputWithError>

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

                <!-- Posta Kodu -->
                <InputWithError :errors="formErrors.postcode">
                    <label>Posta Kodu</label>
                    <input type="text" v-model="formData.postcode" class="form-control">
                </InputWithError>

                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>