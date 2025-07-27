import { ref } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'

export const useViewWrapper = defineStore('viewWrapper', () => {
  const isPushed = ref(false)
  const isPushedBlock = ref(false)
  const pageTitle = ref('Welcome')
  const isLoading = ref(false)
  const cartActive = ref(false)
  const previousButton = ref(false)


  function setPushed(value: boolean) {
    isPushed.value = value
  }
  function setPushedBlock(value: boolean) {
    isPushedBlock.value = value
  }
  function setPageTitle(value: string) {
    pageTitle.value = value
    //set document title
    document.title = value + ' | Bayizone Bayi Portalı'
  }
  function setLoading(value: boolean) {
    isLoading.value = value
  }
  function setCartActive(value: boolean) {
    cartActive.value = value
  }

  function setPreviousButton(value: boolean) {
    previousButton.value = value
  }

  return {
    isPushed,
    isPushedBlock,
    pageTitle,
    isLoading,
    cartActive,
    previousButton,
    setPushed,
    setPushedBlock,
    setPageTitle,
    setLoading,
    setCartActive,
    setPreviousButton

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
