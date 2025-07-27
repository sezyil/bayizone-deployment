<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession';

const userSession = useUserSession();
const userName = computed(() => userSession.user?.fullname)
const planName = computed(() => userSession.planData?.is_trial ? 'Deneme' : userSession.planData?.plan_name)
const endsAt = computed(() => userSession.planData?.is_trial
    ? 'Deneme Bitiş: <br/>' + userSession.planData?.trial_ends_at
    : 'Plan Bitiş: <br/>' + userSession.planData?.ends_at)
const tooltip = ref<HTMLElement | null>(null)
const tooltipActive = ref<boolean>(false)
onMounted(() => {
    //@ts-ignore
    $('[data-toggle="tooltip"]').tooltip()
})

watch(tooltipActive, (value) => {
    if (tooltip.value) {
        //@ts-ignore
        $(tooltip.value).tooltip(value ? 'show' : 'hide')
    }
})


</script>
<template>
    <li class="nav-item nav-item-dropdown-lg dropdown dropdown-user h-100 d-flex align-items-center">
        <span v-if="planName" class="badge badge-success my-3 my-lg-0 ml-lg-3" data-toggle="tooltip" data-html="true"
            ref="tooltip" @click="tooltipActive = !tooltipActive" data-placement="bottom" :title="endsAt">{{ planName
            }}</span>
        <span v-else class="badge badge-danger my-3 my-lg-0 ml-lg-3">Ücretsiz</span>
        <a href="#"
            class="navbar-nav-link navbar-nav-link-toggler dropdown-toggle d-inline-flex align-items-center h-100"
            data-toggle="dropdown">
            <img :src="userSession.getCustomerImage" class="rounded-pill mr-lg-2" alt="" height="34">
            <span class="d-lg-inline-block">{{ userName }}</span>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <RouterLink to="/app/profile" class="dropdown-item"><i class="icon-cog5"></i> Hesap Ayarları</RouterLink>
            <a href="#" class="dropdown-item" @click.prevent="userSession.logoutUser()"><i class="icon-switch2"></i>
                Çıkış</a>
        </div>
    </li>
</template>

<style lang="scss" scoped></style>