<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { get, update } from '/@src/request/request-profile'
import { SwalInstance } from '/@src/shared/common/type-swal';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const isLoading = ref<boolean>(false);
const swal = inject('$swal') as SwalInstance;
const { t } = useI18n()
const formData = ref<any>({
    email: "",
    fullname: "",
    password: "",
    password_confirmation: "",
});

const formErrors = ref<any>({
    fullname: [],
    password: [],
    password_confirmation: [],
});

const resetErrors = () => {
    formErrors.value = {
        fullname: [],
        password: [],
        password_confirmation: [],
    }
}
type FormType = keyof any;

const sendForm = async () => {
    isLoading.value = true
    resetErrors()
    try {
        const { data } = await update(formData.value)
        swal.fire({
            icon: 'success',
            title: t('common.success'),
            text: t('account.updated'),
        }).then(() => {
            location.reload()
        })

    } catch (error) {
        catchFieldError(error, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        });
    }
    isLoading.value = false
}



const getData = async () => {
    isLoading.value = true
    try {
        const { data } = await get()
        const tmpData = data.data as Partial<any>
        formData.value.email = tmpData.email ?? ""
        formData.value.fullname = tmpData.fullname ?? ""
    } catch (error) {
        swal.fire({
            icon: 'error',
            title: t('common.error'),
            text: t('common.data.retrievalError'),
        })
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
            <h6 class="card-title"> {{ t('account.settings') }} </h6>

            <div class="card-body">
                <form @submit.prevent="sendForm" autocomplete="off">

                    <InputWithError :errors="formErrors.fullname">
                        <label>{{ t('common.fullname') }}</label>
                        <input type="text" v-model="formData.fullname" placeholder="Ad-Soyad" class="form-control">
                    </InputWithError>

                    <InputWithError>
                        <label>{{ t('components.users.form.email') }}</label>
                        <input type="email" :value="formData.email" :placeholder="t('components.users.form.email')" disabled class="form-control">
                        <small class="form-text text-danger">{{ t('account.emailNotChangeable') }}</small>
                    </InputWithError>


                    <div class="row">
                        <div class="col-md-6">
                            <InputWithError :errors="formErrors.password">
                                <label>{{ t('components.users.form.password') }}</label>
                                <input type="password" autocomplete="new-password" v-model="formData.password"
                                    :placeholder="t('components.users.form.password')" class="form-control">
                            </InputWithError>
                        </div>
                        <div class="col-md-6">
                            <InputWithError :errors="formErrors.password_confirmation">
                                <label>{{ t('components.users.form.repass') }}</label>
                                <input type="password" autocomplete="new-password"
                                    v-model="formData.password_confirmation"
                                    :placeholder="t('components.users.form.repass')" class="form-control">
                            </InputWithError>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            {{ t('actions.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
