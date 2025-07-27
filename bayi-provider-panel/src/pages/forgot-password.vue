<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useApi } from "../composables/useApi";
import { useSwal } from "../composables/useSwal";
import { useViewWrapper } from "../stores/viewWrapper";
import { catchFieldError } from "../utils/api/catchFormErrors";
const logo = "/images/bayizone_light.png";
const { t, locale } = useI18n();
const router = useRouter();
const api = useApi();
const swal = useSwal();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle(t("pages.auth.forgotPassword.title"));
const currentLocale = computed(() => locale.value)
const currentImage = computed(() => {
    let _style = `background-image: url('/images/bayizone-app-register-${currentLocale.value}.svg');`
    return _style
})
interface IFormErrors {
    code: string[];
    email: string[];
}
const formErrors = ref<IFormErrors>({
    code: [],
    email: [],
});
const formData = ref({
    code: "",
    email: "",
});
type TFormErrors = keyof IFormErrors;
const handleForm = async (e: Event) => {
    e.preventDefault();
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.post("/auth/forgot-password", formData.value);
        swal
            .fire({
                icon: "success",
                title: t("common.success"),
                text: t('pages.auth.forgotPassword.success'),
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
    viewWrapper.setLoading(false);
};
</script>


<template>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
            <div
                class="flex-1  bg-gradient-to-b from-auth-gradient-start to-auth-gradient-end text-center hidden lg:flex">
                <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat" :style="currentImage">
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
                    <div class="mt-6 flex flex-col items-center">

                        <div class="w-full flex-1 mt-8">
                            <div class="mx-auto max-w-xs">
                                <form @submit.prevent="handleForm">
                                    <!-- firm code -->
                                    <input
                                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                                        type="text" :placeholder="t('components.login.code')" v-model="formData.code" />
                                    <div v-if="formErrors.code.length > 0" class="text-red-500 text-xs mt-1">
                                        <span v-for="error in formErrors.code">{{ error }}</span>
                                    </div>
                                    <input
                                        class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                                        type="email" placeholder="Email" v-model="formData.email" />
                                    <div v-if="formErrors.email.length > 0" class="text-red-500 text-xs mt-1">
                                        <span v-for="error in formErrors.email">{{ error }}</span>
                                    </div>


                                    <div class="mt-8 flex flex-col items-center">
                                        <button type="submit"
                                            class="w-full bg-bayi-red hover:bg-bayi-red-hover text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                                            <i class="icon-spinner11 mr-2"></i>
                                            {{ t('pages.auth.forgotPassword.resetPassword') }}
                                        </button>
                                        <p class="text-gray-600 mt-3">
                                            <RouterLink to="/auth" class="text-black hover:text-black">
                                                {{ t('components.login.login') }}
                                            </RouterLink>
                                        </p>
                                    </div>


                                </form>
                            </div>
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
