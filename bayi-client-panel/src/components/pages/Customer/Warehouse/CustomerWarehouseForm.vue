<script setup lang="ts">
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { SwalInstance } from '/@src/shared/common/type-swal';
import CustomerWarehouseApi from '/@src/request/request-customer-warehouses';
import { ICompanyCustomerWarehouseForm, ICompanyCustomerWarehouseFormErrors } from '/@src/shared/form/interface-company-customer-warehouse';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const props = defineProps({
    customer_id: {
        type: String,
        required: true
    },
    warehouse_id: {
        type: String,
    }
})
const apiClass = new CustomerWarehouseApi(props.customer_id ?? undefined);

const formData = ref<ICompanyCustomerWarehouseForm>({
    address: '',
    city_id: 0,
    country_id: 0,
    state_id: 0,
    zip_code: '',
    contact_person: '',
    phone: '',
    email: '',
    contact_person_email: '',
    contact_person_phone: '',
    name: '',
});

const formErrors = ref<ICompanyCustomerWarehouseFormErrors>({
    address: [],
    city_id: [],
    country_id: [],
    state_id: [],
    zip_code: [],
    contact_person: [],
    phone: [],
    email: [],
    contact_person_email: [],
    contact_person_phone: [],
    name: [],
});

const resetErrors = () => {
    formErrors.value = {
        address: [],
        city_id: [],
        country_id: [],
        state_id: [],
        zip_code: [],
        contact_person: [],
        phone: [],
        email: [],
        contact_person_email: [],
        contact_person_phone: [],
        name: [],
    };
}

type FormType = keyof ICompanyCustomerWarehouseFormErrors;
const _redir = () => router.push(`/app/customers/:id?tab=warehouses`.replace(':id', props.customer_id ?? ''))
const sendForm = async () => {
    resetErrors();
    viewWrapper.setLoading(true);

    try {
        if (props.warehouse_id) await apiClass.update(formData.value, props.warehouse_id);
        else await apiClass.add(formData.value);
        swal.fire('Başarılı', 'Kaydedildi!', 'success').then(() => _redir());
    } catch (err: any) {
        catchFieldError(err, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        });
    }
    viewWrapper.setLoading(false);
}

const getData = async () => {
    if (!props.warehouse_id) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await apiClass.get(props.warehouse_id)
        formData.value = data.data as ICompanyCustomerWarehouseForm;
        viewWrapper.setLoading(false);
    } catch (error) {
        catchFieldError(error, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        })?.then(() => {
            _redir();
            viewWrapper.setLoading(false);
        });

    }

}


onMounted(() => {
    if (props.warehouse_id) getData();
});


</script>
<template>
    <div class="card">
        <div class="card-body">
            <form action="#" autocomplete="off" @submit.prevent="sendForm" autosave="off">
                <div class="row">
                    <div class="col-md-12">
                        <InputWithError :errors="formErrors.name">
                            <label>Depo Adı</label>
                            <input type="text" v-model="formData.name" class="form-control" placeholder="Depo Adı"
                                required>
                        </InputWithError>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.email">
                            <label>E-Posta</label>
                            <input type="text" v-model="formData.email" class="form-control" placeholder="E-Posta"
                                required>
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.phone">
                            <label>Telefon</label>
                            <input type="text" v-model="formData.phone" class="form-control" placeholder="Telefon"
                                required>
                        </InputWithError>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.contact_person">
                            <label>İletişim Kişisi</label>
                            <input type="text" v-model="formData.contact_person" class="form-control"
                                placeholder="İletişim Kişisi" required>
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.contact_person_phone">
                            <label>İletişim Kişisi Telefon</label>
                            <input type="text" v-model="formData.contact_person_phone" class="form-control"
                                placeholder="İletişim Kişisi Telefon" required>
                        </InputWithError>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.contact_person_email">
                            <label>İletişim Kişisi E-Posta</label>
                            <input type="text" v-model="formData.contact_person_email" class="form-control"
                                placeholder="İletişim Kişisi E-Posta" required>
                        </InputWithError>
                    </div>
                    <!-- zip code -->

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
                    <div class="col-md-12">
                        <InputWithError :errors="formErrors.address">
                            <label>Adres</label>
                            <textarea v-model="formData.address" class="form-control" placeholder="Adres"
                                required></textarea>
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.zip_code">
                            <label>Posta Kodu</label>
                            <input type="text" v-model="formData.zip_code" class="form-control" placeholder="Posta Kodu"
                                required>
                        </InputWithError>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>