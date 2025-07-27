<script setup lang="ts">
import { useApi } from "/@src/composables/useApi";
import { useSwal } from "/@src/composables/useSwal";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
const isLoading = ref(false);
const router = useRouter();
const api = useApi();
const swal = useSwal();
interface IFormErrors {
    email: string[];
}
const formErrors = ref<IFormErrors>({
    email: [],
});
const formData = ref({
    email: "",
});
type TFormErrors = keyof IFormErrors;
const handleForm = async (e: Event) => {
    e.preventDefault();
    isLoading.value = true;
    try {
        const { data } = await api.post("/auth/forgot-password", formData.value);
        swal
            .fire({
                icon: "success",
                title: "Başarılı",
                text: "Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.",
                timer: 3000,
                showConfirmButton: false,
            })
            .then(() => {
                router.push("/auth");
            });
    } catch (error) {
        catchFieldError(error, (field: TFormErrors, message: any) => {
            formErrors.value[field] = [message];
        });
    }
    isLoading.value = false;
};
</script>
<template>
    <div class="mt-6 flex flex-col items-center">

        <div class="w-full flex-1 mt-8">
            <div class="mx-auto max-w-xs">
                <form @submit.prevent="handleForm">
                    <input
                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                        type="email" placeholder="Email" v-model="formData.email" />
                    <div v-if="formErrors.email.length > 0" class="text-red-500 text-xs mt-1">
                        <span v-for="error in formErrors.email">{{ error }}</span>
                    </div>


                    <div class="mt-8 flex flex-col items-center">
                        <button type="submit"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                            <i class="icon-spinner11 mr-2"></i>
                            Şifremi Sıfırla
                        </button>
                        <p class="text-gray-600 mt-3">
                            Hesabınız var mı? <RouterLink to="/auth/login"
                                class="text-zinc-500 hover:text-zinc-700">Giriş Yapın</RouterLink>
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