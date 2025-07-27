<script setup lang="ts">
import { IProfileTabs } from './ProfileIndex.vue';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useUserSession } from '/@src/stores/userSession';
const userPermission = useUserPermission();
//logout emit
const emit = defineEmits(['update:tabs', 'logout']);
const store = useUserSession();
const props = defineProps({
    tabs: {
        type: Object as PropType<IProfileTabs>,
        required: true
    }
});

const imageEditable = computed(() => userPermission.hasPermission('customer', 'update'));

const handleTabClick = (tab: string) => {
    emit('update:tabs', tab);
}

const handleLogout = () => {
    emit('logout');
}

</script>

<template>
    <div
        class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-left wmin-300 border-0 shadow-none sidebar-expand-xl">

        <!-- Sidebar content -->
        <div class="sidebar-content">

            <!-- Navigation -->
            <div class="card">
                <div class="card-body text-center">
                    <ProfileCompanyImage :image="store.user?.image" :editable="imageEditable" />

                    <h6 class="font-weight-semibold mb-0">{{ store.user?.fullname }}</h6>
                    <span class="d-block opacity-75">{{ store.getUserRoleDescription() }}</span>
                </div>

                <ul class="nav nav-sidebar">
                    <li class="nav-item" v-for="item in tabs.panel" :key="item.target">
                        <a class="nav-link cursor-pointer" data-toggle="tab" @click.prevent="handleTabClick(item.target)"
                            :class="{ active: tabs.active === item.target }">
                            <i :class="item.icon"></i>
                            {{ item.text }}
                        </a>
                    </li>
                    <li class="nav-item-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link cursor-pointer bg-danger-100 text-danger" data-toggle="tab"
                            @click="handleLogout">
                            <i class="icon-switch2"></i>
                            <b>ÇIKIŞ YAP</b>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- /navigation -->

        </div>
        <!-- /sidebar content -->

    </div>
</template>