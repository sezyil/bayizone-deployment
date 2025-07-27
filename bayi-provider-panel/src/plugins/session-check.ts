
import { definePlugin } from '/@src/app'
import { useUserSession } from '/@src/stores/userSession'
import { getUserInfo } from '/@src/request/request-auth'
import { IUser } from '../shared/user/interface-user'
import { toggleMobileMenu } from '../utils/theme_util'
import { useViewWrapper } from '../stores/viewWrapper'

/**
 * Here we are setting up two router navigation guards
 * (note that we can have multiple guards in multiple plugins)
 *
 * We can add meta to pages either by declaring them manualy in the
 * routes declaration (see /@src/router.ts)
 * or by adding a <route> tag into .vue files (see /@src/pages/sidebar/dashboards.ts)
 *
 * <route lang="yaml">
 * meta:
 *   requiresAuth: true
 * </route>
 *
 * <script setup lang="ts">
 *  // TS script
 * </script>
 *
 * <template>
 *  // HTML content
 * </template>
 */
export default definePlugin(async ({ router, api, pinia }) => {
    const userSession = useUserSession(pinia)
    const viewWrapper = useViewWrapper(pinia)
    // 1. Check token validity at app startup
    if (userSession.isLoggedIn) {
        try {
            // Do api request call to retreive user profile.
            const { data } = await getUserInfo()
            userSession.setUser(data.data.user as IUser)
        } catch (err) {
            // delete stored token if it fails
            userSession.logoutUser(true)
        }
    }

    router.beforeEach((to) => {
        viewWrapper.setPreviousButton(true)
        if (to.meta.requiresAuth) {
            if (!userSession.isLoggedIn) {
                // If the page requires auth, check if user is logged in
                // if not, redirect to login page.
                return {
                    path: '/auth',
                    // save the location we were at to come back later
                    query: { redirect: to.fullPath },
                }
            } else {
                toggleMobileMenu(true)
            }
        }
        // If no redirect needed, continue to the page.
    })
})
