<script setup lang="ts">
import PTProfile from './Tabs/PTProfile.vue';
import PTCompany from './Tabs/PTCompany.vue';
import PTServices from './Tabs/PTServices.vue';
import { useUserSession } from '/@src/stores/userSession';
import { useUserPermission } from '/@src/composables/useUserPermission';
const userPermission = useUserPermission();
const customerProfilePermission = useUserPermission().getByName('customer');

interface ITabComponents {
    profile: typeof PTProfile;
    company?: typeof PTCompany;
    services?: typeof PTServices;
}

const profileComponents: ITabComponents = {
    profile: PTProfile,
}

if (customerProfilePermission.update) {
    profileComponents.company = PTCompany;
    profileComponents.services = PTServices;
}

type ProfileTabType = keyof typeof profileComponents;

export interface IProfileTabs {
    active: ProfileTabType;
    panel: Array<{
        target: ProfileTabType;
        text: string;
        icon: string;
    }>;
}
const generatedPanel = computed(() => {
    let tmp: Array<{
        target: ProfileTabType;
        text: string;
        icon: string;
    }> = [
            { target: 'profile', text: 'Hesap Ayarları', icon: 'icon-user' },
        ];

    if (userPermission.hasPermission('customer', 'update')) {
        tmp.push({ target: 'company', text: 'Şirket Ayarları', icon: 'icon-office' });
        tmp.push({ target: 'services', text: 'Hizmetler', icon: 'icon-cogs' });
    }
    return tmp;
})
const tabs = ref<IProfileTabs>({
    active: 'profile',
    panel: generatedPanel.value
});

const checkParameter = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');

    //if tab is null or tab is not in profileComponents
    if (!tab || !(tab in profileComponents)) {
        return;
    }
    tabs.value.active = tab as ProfileTabType;

}

const handleTabClick = (tab: string) => {
    tabs.value.active = tab as ProfileTabType;
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('tab', tab);
    window.history.replaceState({}, '', `${location.pathname}?${urlParams}`);
}

const handleLogout = () => useUserSession().logoutUser()


onMounted(() => {
    checkParameter();
});

</script>
<template>
    <div class="d-lg-flex align-items-lg-start">

        <!-- Left sidebar component -->
        <ProfileSidebar :tabs="tabs" @update:tabs="handleTabClick" @logout="handleLogout" />
        <!-- /left sidebar component -->


        <!-- Right content -->
        <div class="tab-content flex-1">
            <div class="tab-pane active">
                <component :is="profileComponents[tabs.active]" />
            </div>
        </div>
        <!-- /right content -->

    </div>
</template>