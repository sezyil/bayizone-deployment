<script setup lang="ts">
import { useApi } from "/@src/composables/useApi";
import { useSwal } from "/@src/composables/useSwal";
import { useUserSession } from "/@src/stores/userSession";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const router = useRouter();
const swal = useSwal();
const api = useApi();
const userSession = useUserSession();
const viewWrapper = useViewWrapper();
const setLoading = viewWrapper.setLoading;
const getQueryParameters = () => {
    const params = new URLSearchParams(window.location.search);
    return {
        user: params.get("u"),
        token: params.get("t"),
    };
};
const { user, token } = getQueryParameters();
const hasInfo = ref(false);
const formData = ref({
    token: "",
    user: "",
    password: "",
    password_confirmation: "",
});

interface IFormErrors {
    password: string[];
    password_confirmation: string[];
    token: string[];
    user: string[];
}

const formErrors = ref<IFormErrors>({
    password: [],
    password_confirmation: [],
    token: [],
    user: [],
});

type TFormErrors = keyof IFormErrors;

const resetFormErrors = () => {
    formErrors.value = {
        password: [],
        password_confirmation: [],
        token: [],
        user: [],
    };
};

const handleForm = async (e: Event) => {
    e.preventDefault();
    setLoading(true);
    resetFormErrors();
    try {
        const { data } = await api.post("/auth/reset-password", formData.value);
        swal.fire({
            icon: "success",
            title: "Başarılı",
            text: "Şifreniz başarıyla değiştirildi.",
            timer: 3000,
            showConfirmButton: false,
        }).then(() => {
            router.push("/auth");
        });
    } catch (error) {
        catchFieldError(error, (field: TFormErrors, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        });

        //if form errors has token error 
        if (formErrors.value.token.length > 0 || formErrors.value.user.length > 0) {
            swal.fire({
                icon: "error",
                title: "Hata",
                text: "Geçersiz istek. Bu sayfaya doğrudan erişim sağlayamazsınız/Geçersiz istek. Eğer hata alma durumunuz devam ederse lütfen bizimle iletişime geçiniz.",
            }).then(() => {
                router.push("/auth");
            });
        }
    }
    setLoading(false);
};

onMounted(async () => {
    if (userSession.isLoggedIn) {
        await router.push("/app");
    } else {
        if (!user || !token) {
            swal.fire({
                icon: "error",
                title: "Hata",
                text: "Geçersiz istek",
            }).then(() => {
                router.push("/auth");
            });
        } else {
            hasInfo.value = true;
            formData.value = {
                token,
                user,
                password: "",
                password_confirmation: "",
            };
        }
    }
});
</script>
<template>
    <div class="mt-6 flex flex-col items-center" v-if="hasInfo">

        <div class="w-full flex-1 mt-8">
            <!-- Yeni Şifrenizi girin Yazısı -->
            <div class="mx-auto max-w-xs">
                <h1 class="text-2xl text-center text-indigo-800 mb-6">Yeni Şifrenizi Oluşturun</h1>

                <form @submit.prevent="handleForm">

                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                        type="password" placeholder="Yeni Şifre" v-model="formData.password" />
                    <div v-if="formErrors.password.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in formErrors.password">{{ error }}</span>
                    </div>

                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                        type="password" placeholder="Yeni Şifre Tekrar" v-model="formData.password_confirmation" />
                    <div v-if="formErrors.password_confirmation.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in formErrors.password_confirmation">{{ error }}</span>
                    </div>

                    <div class="mt-8 flex flex-col items-center">
                        <button type="submit"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin mr-2" v-if="viewWrapper.isLoading"></i>
                            <i class="fas fa-key mr-2" v-else></i>
                            Şifremi Değiştir
                        </button>
                        <p class="text-gray-600 mt-3">
                            Hesabınız var mı? <RouterLink to="/auth/login" class="text-zinc-500 hover:text-zinc-700">
                                Giriş Yapın</RouterLink>
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