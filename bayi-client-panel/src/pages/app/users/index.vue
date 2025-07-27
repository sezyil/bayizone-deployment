<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Kullanıcı Listesi');
const permission = useUserPermission().getByName('user')
const userSession = useUserSession();

const left_user_count = computed(() => userSession.planData?.left_user_count ?? 0);
if (!permission.view) {
    swalPermissionDenied(() => router.push('/app'))
}

const buttonText = computed(() => {
    return left_user_count.value <= 0 ? 'Kullanıcı Ekleme Hakkınız Kalmadı' : `Kullanıcı Ekle (${left_user_count.value} kaldı)`;
})

const redirectToCreate = () => {
    if (left_user_count.value <= 0) {
        useSwal().fire({
            title: 'Kullanıcı Ekleme Hakkınız Kalmadı',
            text: 'Kullanıcı eklemek için lütfen aboneliğinizi yükseltin.',
            icon: 'warning',
        });
    } else {
        router.push('/app/users/create')
    }
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission.create">
            <VButtonCreate @click="redirectToCreate" :text="buttonText" :disabled="left_user_count <= 0" />
        </template>
        <UserIndex v-if="permission.view" :can-delete="permission.delete" :can-update="permission.update" />
    </PageContent>
</template>



<style scoped></style>