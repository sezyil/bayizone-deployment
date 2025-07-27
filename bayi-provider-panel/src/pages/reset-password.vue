<script setup lang="ts">
import { useApi } from "../composables/useApi";
import { useSwal } from "../composables/useSwal";
import { useUserSession } from "../stores/userSession";
import { catchFieldError } from "../utils/api/catchFormErrors";
import { useViewWrapper } from "../stores/viewWrapper";
import { useI18n } from "vue-i18n";
const router = useRouter();
const swal = useSwal();
const api = useApi();
const { t, locale } = useI18n();
const viewWrapper = useViewWrapper();
const logo = "/images/bayizone_light.png";
const userSession = useUserSession();
const currentLocale = computed(() => locale.value)
const currentImage = computed(() => {
    let _style = `background-image: url('/images/bayizone-app-register-${currentLocale.value}.svg');`
    return _style
})
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
    viewWrapper.setLoading(true);
    resetFormErrors();
    try {
        const { data } = await api.post("/auth/reset-password", formData.value);
        swal.fire({
            icon: "success",
            title: t('common.success'),
            text: t('pages.auth.forgotPassword.successChange'),
            timer: 3000,
            showConfirmButton: false,
        }).then(() => {
            router.push("/auth");
        });
    } catch (error) {
        catchFieldError(error, (field: TFormErrors, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        });
    }
    viewWrapper.setLoading(false);
};

onMounted(async () => {
    if (userSession.isLoggedIn) {
        await router.push("/");
    } else {
        if (!user || !token) {
            swal.fire({
                icon: "error",
                title: t('common.error'),
                text: t('pages.auth.forgotPassword.invalidLink'),
            }).then(() => {
                router.push("/");
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

    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
            <div
                class="flex-1 bg-gradient-to-b from-auth-gradient-start to-auth-gradient-end text-center hidden lg:flex">
                <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat"
                    :style="currentImage">
                </div>
            </div>
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div>
                    <img :src="logo" class="w-32 mx-auto" />
                </div>
                <div>
                    <!-- Loader -->
                    <div class="w-full h-full fixed top-0 left-0 bg-white opacity-75 z-50" v-if="viewWrapper.isLoading">
                        <div class="flex justify-center items-center mt-[50vh]">
                            <i class="icon-spinner9 spinner text-body icon-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex flex-col items-center" v-if="hasInfo">

                    <div class="w-full flex-1 mt-8">
                        <!-- Yeni Şifrenizi girin Yazısı -->
                        <div class="mx-auto max-w-xs">
                            <h1 class="text-2xl text-center text-zinc-800 mb-6">{{
                                t('components.login.createNewPassword') }}</h1>

                            <form @submit.prevent="handleForm">

                                <input
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                                    type="password" :placeholder="t('pages.auth.forgotPassword.newpassword')"
                                    v-model="formData.password" />
                                <div v-if="formErrors.password.length > 0" class="text-red-500 text-xs mt-1">
                                    <span v-for="error in formErrors.password">{{ error }}</span>
                                </div>

                                <input
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                                    type="password" :placeholder="t('pages.auth.forgotPassword.repass')"
                                    v-model="formData.password_confirmation" />
                                <div v-if="formErrors.password_confirmation.length > 0"
                                    class="text-red-500 text-xs mt-1">
                                    <span v-for="error in formErrors.password_confirmation">{{ error }}</span>
                                </div>

                                <div class="mt-8 flex flex-col items-center">
                                    <button type="submit"
                                        class="w-full bg-bayi-red hover:bg-bayi-red-hover text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        {{ t('components.login.recoverPassword') }}
                                    </button>
                                    <p class="text-gray-600 mt-3">
                                        <RouterLink to="/auth" class="text-black hover:text-black">{{
                                            t('components.login.login') }}</RouterLink>
                                    </p>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</template>

<style scoped>
@tailwind base;
@tailwind components;
@tailwind utilities;
</style>
