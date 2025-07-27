<script setup lang="ts">
import { useUserSession } from "/@src/stores/userSession";
import {
  IAuthLoginForm,
  IAuthLoginFormError,
} from "/@src/shared/form/interface-auth";
import { requestLogin } from "/@src/request/request-auth";
import { useViewWrapper } from "/@src/stores/viewWrapper";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
import { SwalInstance } from "/@src/shared/common/type-swal";
import { useI18n } from "vue-i18n";
import { IUser } from "/@src/shared/user/interface-user";
const route = useRoute();
const redirect = route.query.redirect as string;
const userSession = useUserSession();
const swal = inject("$swal") as SwalInstance;
const formSending = ref(false);
const router = useRouter();
const { locale, t } = useI18n()
const logo = "/images/bayizone_light.png"
const defaultLocale = useStorage('locale', 'tr')
const currentLocale = computed(() => locale.value)
const currentImage = computed(() => {
  let _style = `background-image: url('/images/bayizone-app-login-${currentLocale.value}.svg');`
  return _style
})

const formData = ref<IAuthLoginForm>({
  email: "",
  password: "",
  code: ""
});
const errors = ref<IAuthLoginFormError>({
  email: [],
  password: [],
  code: []
});
type FormField = "email" | "password" | "code";

const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle(t('pages.auth.login.title'))

const resetErrors = () => {
  errors.value = {
    email: [],
    password: [],
    code: []
  };
};

const sendForm = async () => {
  viewWrapper.setLoading(true);
  resetErrors();
  try {
    const { data } = await requestLogin(formData.value);
    let _token = data.data.token;
    userSession.setToken(_token);
    userSession.setUser(data.data.user as IUser);
    defaultLocale.value = userSession.user?.language as string
    locale.value = defaultLocale.value

    swal
      .fire({
        title: t('common.redirecting'),
        text: t('components.login.success'),
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
  <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
    <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
      <div class="flex-1  bg-gradient-to-b from-auth-gradient-start to-auth-gradient-end text-center hidden lg:flex">
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
        </div>
        <div class="mt-6 flex flex-col items-center">

          <div class="w-full flex-1 mt-8">
            <div class="mx-auto max-w-xs">
              <form @submit.prevent="sendForm">
                <!-- firm code -->
                <input
                  class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                  name="firm_code" type="text" :placeholder="t('components.login.code')" v-model="formData.code" />
                <div v-if="errors.code.length > 0" class="text-red-500 text-xs mt-1">
                  <span v-for="error in errors.code">{{ error }}</span>
                </div>
                <input
                  class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                  type="email" placeholder="Email" v-model="formData.email" />
                <div v-if="errors.email.length > 0" class="text-red-500 text-xs mt-1">
                  <span v-for="error in errors.email">{{ error }}</span>
                </div>
                <input
                  class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5"
                  type="password" :placeholder="t('components.login.password')" v-model="formData.password" />
                <div v-if="errors.password.length > 0" class="text-red-500 text-xs mt-1">
                  <span v-for="error in errors.password">{{ error }}</span>
                </div>

                <div class="mt-8 flex flex-col items-center">
                  <button type="submit"
                    class="w-full bg-bayi-red hover:bg-bayi-red-hover text-white font-semibold py-3 px-6 rounded-lg shadow-md focus:outline-none focus:shadow-outline transition-all duration-300 ease-in-out flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    {{ t('components.login.login') }}
                  </button>
                  <p class="text-gray-800 mt-3">
                    <RouterLink to="/forgot-password" class="text-black-500 hover:text-black">{{
                      t('components.login.forgotPassword') }}</RouterLink>
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
