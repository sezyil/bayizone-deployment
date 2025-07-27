<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();
const userSession = useUserSession();
const swal = useSwal();
const permission = useUserPermission().getByName('user')
const router = useRouter();
viewWrapper.setPageTitle('Kullanıcı Oluştur');

if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'))
}

const left_user_count = computed(() => userSession.planData?.left_user_count ?? 0);

onBeforeMount(() => {
    if (left_user_count.value <= 0) {
        swal.fire({
            title: 'Kullanıcı Ekleme Hakkınız Kalmadı',
            text: 'Kullanıcı eklemek için lütfen aboneliğinizi yükseltin.',
            icon: 'warning',
        }).then(() => router.push('/app/users'))
    }
})


</script>
<template>
    <PageContent>
        <UserForm v-if="permission.create && left_user_count > 0" />
    </PageContent>
</template>

<style scoped></style>