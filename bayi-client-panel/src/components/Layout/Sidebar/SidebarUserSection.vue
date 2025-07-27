<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const userStore = useUserSession();
const viewWrapper = useViewWrapper();

//sidebar sidebar-dark sidebar-main watch
const sidebarElement = ref<HTMLElement | null>(null);



</script>
<template>
    <div class="sidebar-section sidebar-user my-1">
        <div class="sidebar-section-body">
            <div class="media">
                <a href="#" class="mr-3">
                    <img :src="userStore.getCustomerImage" class="rounded-circle" alt="">
                </a>

                <div class="media-body">
                    <div class="font-weight-semibold">{{ userStore.user?.fullname }}</div>
                    <div class="font-size-sm line-height-sm opacity-50">
                        {{ userStore.getUserRoleDescription() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>