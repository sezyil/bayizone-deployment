import { ref } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
type AuthPageMode = 'login' | 'other'
export const useViewWrapper = defineStore('viewWrapper', () => {
  const isPushed = ref(false)
  const isPushedBlock = ref(false)
  const pageTitle = ref('Bayizone')
  const isLoading = ref(false)

  const authPageMode = ref<AuthPageMode>('login')

  function setAuthPageMode(mode: AuthPageMode) {
    authPageMode.value = mode
  }

  function setPushed(value: boolean) {
    isPushed.value = value
  }
  function setPushedBlock(value: boolean) {
    isPushedBlock.value = value
  }
  function setPageTitle(value: string) {
    pageTitle.value = value
    //set document title
    document.title = value + ' | Bayizone'
  }
  function setLoading(value: boolean) {
    isLoading.value = value
  }

  return {
    isPushed,
    isPushedBlock,
    pageTitle,
    setPushed,
    setPushedBlock,
    setPageTitle,
    authPageMode,
    setAuthPageMode,
    isLoading,
    setLoading

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
  import.meta.hot.accept(acceptHMRUpdate(useViewWrapper, import.meta.hot))
}
