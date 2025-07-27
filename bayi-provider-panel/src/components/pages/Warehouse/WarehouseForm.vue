<script setup lang="ts">
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { SwalInstance } from '/@src/shared/common/type-swal';

import { IWarehouseForm, IWarehouseFormErrors } from '/@src/shared/form/interface-warehouse';
import WarehouseApi from '/@src/request/request-warehouses';
import { useI18n } from 'vue-i18n';
const viewWrapper = useViewWrapper();
const router = useRouter();
const { t } = useI18n();
const swal = inject('$swal') as SwalInstance;
const props = defineProps({
    warehouse_id: {
        type: String,
    }
})
const apiClass = new WarehouseApi();

const formData = ref<IWarehouseForm>({
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

const formErrors = ref<IWarehouseFormErrors>({
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

type FormType = keyof IWarehouseFormErrors;
const _redir = () => router.push('/app/warehouses')
const sendForm = async () => {
    resetErrors();
    viewWrapper.setLoading(true);

    try {
        if (props.warehouse_id) await apiClass.update(formData.value, props.warehouse_id);
        else await apiClass.add(formData.value);
        swal.fire(t('common.success'), t('actions.processSuccess'), 'success').then(() => _redir());
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
        formData.value = data.data as IWarehouseForm;
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
                            <label>{{ t('components.warehouses.index.name') }}</label>
                            <input type="text" v-model="formData.name" class="form-control"
                                :placeholder="t('components.warehouses.index.name')" required>
                        </InputWithError>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.email">
                            <label>{{ t('components.users.form.email') }}</label>
                            <input type="text" v-model="formData.email" class="form-control"
                                :placeholder="t('components.users.form.email')" required>
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.phone">
                            <label>{{ t('common.phone') }}</label>
                            <input type="text" v-model="formData.phone" class="form-control"
                                :placeholder="t('common.phone')" required>
                        </InputWithError>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.contact_person">
                            <label>{{ t('components.warehouses.form.contactPerson') }}</label>
                            <input type="text" v-model="formData.contact_person" class="form-control"
                                :placeholder="t('components.warehouses.form.contactPerson')" required>
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.contact_person_phone">
                            <label>{{ t('components.warehouses.form.contactPersonPhone') }}</label>
                            <input type="text" v-model="formData.contact_person_phone" class="form-control"
                                :placeholder="t('components.warehouses.form.contactPersonPhone')" required>
                        </InputWithError>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.contact_person_email">
                            <label>{{ t('components.warehouses.form.contactPersonMail') }}</label>
                            <input type="text" v-model="formData.contact_person_email" class="form-control"
                                :placeholder="t('components.warehouses.form.contactPersonMail')" required>
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
                            <label>{{ t('common.address') }}</label>
                            <textarea v-model="formData.address" class="form-control" :placeholder="t('common.address')"
                                required></textarea>
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.zip_code">
                            <label>{{ t('common.zipcode') }}</label>
                            <input type="text" v-model="formData.zip_code" class="form-control"
                                :placeholder="t('common.zipcode')" required>
                        </InputWithError>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        {{ t('actions.save') }} <i class="icon-paperplane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>