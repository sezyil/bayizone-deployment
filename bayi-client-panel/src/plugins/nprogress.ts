import { definePlugin } from '/@src/app'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

export default definePlugin(({ router }) => {
  if (import.meta.env.SSR) {
    return
  }

  const _config: Partial<NProgress.NProgressOptions> = {
    showSpinner: false,
  }

  NProgress.configure(_config)
  router.beforeEach(() => {
    NProgress.start()
  })
  router.afterEach(() => {
    NProgress.done()
  })
})
