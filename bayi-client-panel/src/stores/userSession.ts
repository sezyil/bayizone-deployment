import SweetAlert from 'sweetalert2';
import { acceptHMRUpdate, defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useStorage } from '@vueuse/core'
import { IUser, IUserSubscription } from '/@src/shared/user/interface-user';
import { useApi } from '../composables/useApi';
import { GLOB_URIS, get_product_image_url } from '../utils/GLOB_URIS';
const api = useApi()



export const useUserSession = defineStore('userSession', () => {
  // token will be synced with local storage
  // @see https://vueuse.org/core/usestorage/
  const token = useStorage('token', '')

  const user = ref<IUser>()
  const permissions = ref<string[]>([])
  const planData = ref<IUserSubscription | undefined>()

  const isLoggedIn = computed(() => token.value !== undefined && token.value !== '')

  function setUser(newUser: IUser) {
    user.value = newUser
  }

  function setPermissions(newPermissions: string[]) {
    permissions.value = newPermissions
  }

  function setPlanData(newPlanData: IUserSubscription) {
    if (newPlanData) {
      planData.value = { ...planData.value, ...newPlanData }
    }
  }

  const isSuperUser = computed(() => user.value?.is_super_user)

  const subscriptionUpdater = {
    user: {
      increment: () => { planData.value!.left_user_count++ },
      decrement: () => { planData.value!.left_user_count-- },
    },
    provider: {
      increment: () => { planData.value!.left_provider_panel_count++ },
      decrement: () => { planData.value!.left_provider_panel_count-- },
    },
  }

  function setToken(newToken: string) {
    token.value = newToken
  }

  const getCustomerImage = computed(() => {
    let img = user.value?.image
    return img ? get_product_image_url(img) : GLOB_URIS.NO_IMAGE
  });

  function getUserRoleDescription() {
    return user.value?.role
  }

  const wizardStatus = computed(() => ({
    companyInfo: user?.value?.hasCompanyInfo,
    hasImage: user?.value?.image ? true : false,
  }))

  const wizardFinished = computed(() => wizardStatus.value.companyInfo && wizardStatus.value.hasImage);

  async function logoutUser(fromTimeout: boolean = false) {
    if (!fromTimeout) await api.post('/auth/logout');
    token.value = ''
    user.value = undefined
    let msg = fromTimeout ? "Oturumunuzun süresi doldu" : "Çıkış Yapıldı"
    SweetAlert.fire({
      icon: 'warning',
      title: msg,
      showConfirmButton: false,
      timer: 1500
    }).then(() => {
      window.location.reload()
    })
  }

  return {
    user,
    permissions,
    planData,
    token,
    isLoggedIn,
    logoutUser,
    setUser,
    setPermissions,
    setToken,
    setPlanData,
    subscriptionUpdater,
    getUserRoleDescription,
    isSuperUser,
    getCustomerImage,
    wizardStatus,
    wizardFinished
  } as const
})

/**
 * Pinia supports Hot Module replacement so you can edit your stores and
 * interact with them directly in your app without reloading the page.
 *
 * @see https://pinia.esm.dev/cookbook/hot-module-replacement.html
 * @see https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useUserSession, import.meta.hot))
}
