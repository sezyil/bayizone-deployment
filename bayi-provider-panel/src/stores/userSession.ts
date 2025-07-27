import SweetAlert from 'sweetalert2';
import { acceptHMRUpdate, defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useStorage } from '@vueuse/core'
import { IUser } from '/@src/shared/user/interface-user';
import { useApi } from '../composables/useApi';
import { GLOB_URIS, get_product_image_url } from '../utils/global_uris';
const api = useApi()



export const useUserSession = defineStore('userSession', () => {
  // token will be synced with local storage
  // @see https://vueuse.org/core/usestorage/
  const token = useStorage('token', '')

  const user = ref<IUser>()

  const isLoggedIn = computed(() => token.value !== undefined && token.value !== '')

  function setUser(newUser: IUser) {
    user.value = newUser
  }

  function setToken(newToken: string) {
    token.value = newToken
  }

  const getCustomerImage = computed(() => {
    let img = user.value?.image
    return img ? get_product_image_url(img) : GLOB_URIS.NO_IMAGE
  });

  async function logoutUser(fromTimeout: boolean = false) {
    if (!fromTimeout) await api.post('/auth/logout');
    token.value = ''
    user.value = undefined
    window.location.reload()
  }

  return {
    user,
    token,
    isLoggedIn,
    logoutUser,
    setUser,
    setToken,
    getCustomerImage,
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
