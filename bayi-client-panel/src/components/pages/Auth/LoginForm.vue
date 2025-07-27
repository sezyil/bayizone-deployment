<script setup lang="ts">
import { useUserSession } from "/@src/stores/userSession";
import {
    IAuthLoginForm,
    IAuthLoginFormError,
} from "/@src/shared/form/interface-auth";
import { requestLogin } from "/@src/request/request-auth";
import { useViewWrapper } from "/@src/stores/viewWrapper";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
import { useSwal } from "/@src/composables/useSwal";
const route = useRoute();
const redirect = route.query.redirect as string;
const userSession = useUserSession();
const swal = useSwal();
const formSending = ref(false);
const router = useRouter();
const formData = ref<IAuthLoginForm>({
    email: "",
    password: "",
});
const errors = ref<IAuthLoginFormError>({
    email: [],
    password: [],
});
type FormField = "email" | "password";

const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle("Kullanıcı Girişi");

const resetErrors = () => {
    errors.value = {
        email: [],
        password: [],
    };
};

const sendForm = async () => {
    viewWrapper.setLoading(true);
    resetErrors();
    try {
        const { data } = await requestLogin(formData.value);
        let _token = data.data.token;
        userSession.setToken(_token);
        userSession.setUser(data.data.user);
        userSession.setPermissions(data.data.permissions);
        userSession.setPlanData(data.data?.subscription);
        swal
            .fire({
                title: "Yönlendiriliyorsunuz",
                text: "Giriş Başarılı",
                icon: "success",
                showConfirmButton: false,
                timer: 1500,
            })
            .then(async () => {
                let redir = redirect ? redirect : "/app";
                await router.push(redir);
                viewWrapper.setLoading(false);
            });
    } catch (error) {
        viewWrapper.setLoading(false);
        catchFieldError(error, (field: FormField, fieldErr: any) => {
            errors.value[field].push(fieldErr);
        });
    }
};
</script>
<template>
    <div class="mt-6 flex flex-col items-center">

        <div class="w-full flex-1 mt-8">
            <div class="mx-auto max-w-xs">
                <form @submit.prevent="sendForm">

                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                        type="email" placeholder="Email" v-model="formData.email" />
                    <div v-if="errors.email.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in errors.email">{{ error }}</span>
                    </div>
                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                        type="password" placeholder="Şifre" v-model="formData.password" />
                    <div v-if="errors.password.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in errors.password">{{ error }}</span>
                    </div>

                    <div class="mt-8 flex flex-col items-center">
                        <button type="submit"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Giriş Yap
                        </button>
                        <p class="text-gray-600 mt-3">
                            Hesabınız yok mu? <RouterLink to="/auth/register"
                                class="text-zinc-500 hover:text-zinc-700">Hemen kayıt olun</RouterLink>
                        </p>
                        <p class="text-gray-800 mt-3">
                            Şifrenizi mi unuttunuz? <RouterLink to="/auth/forgot-password"
                                class="text-zinc-500 hover:text-zinc-700">Şifremi sıfırla</RouterLink>
                        </p>
                    </div>




                </form>
            </div>
        </div>
    </div>
</template>



<style scoped>
@tailwind base;
@tailwind components;
@tailwind utilities;
</style>