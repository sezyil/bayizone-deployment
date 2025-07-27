<script setup lang="ts">
import { IProfileUserForm, IProfileUserFormErrors } from '/@src/shared/form/interface-profile-user';
import { get, update } from '/@src/request/request-profile'
import { SwalInstance } from '/@src/shared/common/type-swal';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useSwal } from '/@src/composables/useSwal';
const isLoading = ref<boolean>(false);
const swal = useSwal();
const formData = ref<IProfileUserForm>({
    email: "",
    fullname: "",
    password: "",
    password_confirmation: "",
});

const formErrors = ref<IProfileUserFormErrors>({
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
type FormType = keyof IProfileUserFormErrors;

const sendForm = async () => {
    isLoading.value = true
    resetErrors()
    try {
        const { data } = await update(formData.value)
        swal.fire({
            icon: 'success',
            title: 'Başarılı',
            text: 'Bilgileriniz güncellendi',
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
        const tmpData = data.data as Partial<IProfileUserForm>
        formData.value.email = tmpData.email ?? ""
        formData.value.fullname = tmpData.fullname ?? ""
    } catch (error) {
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Bilgileriniz getirilirken bir hata oluştu',
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
            <h6 class="card-title"> Hesap Ayarları </h6>

            <div class="card-body">
                <form @submit.prevent="sendForm" autocomplete="off">

                    <InputWithError :errors="formErrors.fullname">
                        <label>Ad-Soyad</label>
                        <input type="text" v-model="formData.fullname" placeholder="Ad-Soyad" class="form-control">
                    </InputWithError>

                    <InputWithError>
                        <label>Email</label>
                        <input type="email" :value="formData.email" placeholder="Email" disabled class="form-control">
                        <small class="form-text text-danger">Email adresiniz değiştirilemez</small>
                    </InputWithError>


                    <div class="row">
                        <div class="col-md-6">
                            <InputWithError :errors="formErrors.password">
                                <label>Şifre</label>
                                <input type="password" autocomplete="new-password" v-model="formData.password"
                                    placeholder="Şifre" class="form-control">
                            </InputWithError>
                        </div>
                        <div class="col-md-6">
                            <InputWithError :errors="formErrors.password_confirmation">
                                <label>Şifre Tekrar</label>
                                <input type="password" autocomplete="new-password"
                                    v-model="formData.password_confirmation" placeholder="Şifre Tekrar"
                                    class="form-control">
                            </InputWithError>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
