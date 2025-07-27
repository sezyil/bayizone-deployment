
import { definePlugin } from '/@src/app'
import { useUserSession } from '/@src/stores/userSession'
import { getUserInfo } from '/@src/request/request-auth'
import { IUser } from '../shared/user/interface-user'
import { toggleMobileMenu } from '../utils/theme_util'

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
const returnUpgrade = () => ({
    path: '/app/plans',
    query: {
        'upgrade': true,
    }
})
export default definePlugin(async ({ router, api, pinia }) => {
    const userSession = useUserSession(pinia)
    const planData = computed(() => userSession.planData)
    // 1. Check token validity at app startup
    if (userSession.isLoggedIn) {
        try {
            // Do api request call to retreive user profile.
            const { data } = await getUserInfo()
            userSession.setUser(data.data.user as IUser)
            userSession.setPermissions(data.data.permissions)
            userSession.setPlanData(data.data.subscription)
        } catch (err) {
            // delete stored token if it fails
            userSession.logoutUser(true)
        }
    }
    //@ts-ignore
    router.beforeEach((to) => {
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
                const planData = computed(() => userSession.planData)
                //for plan store data
                //get path
                const path = to.path
                const currentMeta = to.meta
                /*
                catalog_management
                proforma_invoices
                create_proposal
                proposal_management
                sales_management
                simple_accounting
                 */
                switch (true) {
                    case path.includes('/app/offers') && !planData.value:
                    case path.includes('/app/offer_requests') && !planData.value:
                    case path.includes('/app/customer_orders') && !planData.value?.sales_management:
                    case path.includes('/app/bank_accounts') && !planData.value?.simple_accounting:
                    case path.includes('/app/transactions') && !planData.value?.simple_accounting:
                        return returnUpgrade();
                    default:
                        break;
                }
            }
            // If no redirect needed, continue to the page.
        }
    })
})
