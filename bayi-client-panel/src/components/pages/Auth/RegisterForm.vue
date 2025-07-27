<script setup lang="ts">
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { requestRegister } from '/@src/request/request-auth';
import { IAuthRegisterForm, IAuthRegisterFormError } from '/@src/shared/form/interface-auth';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useSwal } from '/@src/composables/useSwal';
import InputText from 'primevue/inputtext';
const router = useRouter();
const viewWrapper = useViewWrapper();
const swal = useSwal();
const errors = ref<IAuthRegisterFormError>({
    fullname: [],
    email: [],
    phone: [],
    password: [],
    terms: []
});
const privacyPolicyDialogVisible = ref(false);
const resetErrors = () => {
    errors.value = {
        fullname: [],
        phone: [],
        email: [],
        password: [],
        terms: []
    }
}

const formData = ref<IAuthRegisterForm>({
    fullname: "",
    email: "",
    phone: "",
    password: "",
    terms: false
});

const passwordVisible = ref(false);

type FormFieldType = keyof IAuthRegisterForm;

const sendForm = async (e: Event) => {
    e.preventDefault();
    viewWrapper.setLoading(true);
    try {
        resetErrors();
        const response = await requestRegister(formData.value);
        await swal.fire({
            title: 'Başarılı',
            text: 'Kayıt işlemi başarılı. Mail adresinize gelen aktivasyon linki ile hesabınızı aktifleştirin.',
            icon: 'success',
            showConfirmButton: false,
            timer: 1500,
        });
        viewWrapper.setLoading(false);
        router.push('/auth/login');
    } catch (error) {
        catchFieldError(error, (field: FormFieldType, fieldErr: any) => {
            errors.value[field].push(fieldErr);
        });
        viewWrapper.setLoading(false);
    }
}

const handlePpDialogAccept = (value: boolean) => {
    formData.value.terms = value;
    privacyPolicyDialogVisible.value = false;
}
</script>
<template>
    <div class="mt-6 flex flex-col items-center">


        <div class="w-full flex-1 mt-8">
            <div class="mx-auto max-w-xs">
                <form @submit.prevent="sendForm">

                    <!-- fullname -->
                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                        type="text" placeholder="Ad Soyad" v-model="formData.fullname" required/>
                    <div v-if="errors.fullname.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in errors.fullname">{{ error }}</span>
                    </div>

                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                        type="email" placeholder="Email" v-model="formData.email" required/>
                    <div v-if="errors.email.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in errors.email">{{ error }}</span>
                    </div>

                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                        type="text" placeholder="Telefon" v-model="formData.phone" maxlength="11" required/>
                    <div v-if="errors.phone.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in errors.phone">{{ error }}</span>
                    </div>

                    <!-- password input with eye -->
                    <div class="relative w-full mt-5">
                        <input
                            class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                            :type="passwordVisible ? 'text' : 'password'" placeholder="Şifre" required
                            v-model="formData.password" />
                        <span @click="passwordVisible = !passwordVisible"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                            <svg v-if="passwordVisible" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.532 1.838-1.626 3.44-3.044 4.632M15 12a3 3 0 01-6 0m0 0a3 3 0 016 0m3.546 5.032A9.955 9.955 0 0112 19c-4.477 0-8.268-2.943-9.542-7" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="h-6 w-6 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A9.959 9.959 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.959 9.959 0 012.447-4.032m2.497-2.497A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7-.538 1.849-1.645 3.456-3.073 4.651M3 3l18 18" />
                            </svg>
                        </span>
                    </div>
                    <div v-if="errors.password.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in errors.password">{{ error }}</span>
                    </div>

                    <div class="mt-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remember" v-model="formData.terms"
                                class="form-checkbox text-blue-600 h-5 w-5" :class="errors.terms">
                            <span class="ml-2 text-sm font-medium cursor-pointer"
                                @click.prevent="privacyPolicyDialogVisible = true">Kullanım
                                Koşulları ve Gizlilik Politikası'nı okudum
                                ve kabul ediyorum.</span>
                        </label>
                        <div v-if="errors.terms.length > 0" class="text-red-500 text-xs mt-1">
                            <span v-for="error in errors.terms">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mt-8 flex flex-col items-center">
                        <button type="submit"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                            <i class="fas fa-user-plus mr-2"></i>
                            Kayıt Ol
                        </button>
                        <p class="text-gray-600 mt-3">
                            Hesabınız var mı? <RouterLink to="/auth/login" class="text-zinc-500 hover:text-zinc-700">
                                Giriş Yapın</RouterLink>
                        </p>
                    </div>

                </form>

                <PrivacyPolicyDialog :visible="privacyPolicyDialogVisible" @close="privacyPolicyDialogVisible = false"
                    @update:accept="handlePpDialogAccept" />
            </div>
        </div>
    </div>
</template>



<style scoped>
@tailwind base;
@tailwind components;
@tailwind utilities;
</style>