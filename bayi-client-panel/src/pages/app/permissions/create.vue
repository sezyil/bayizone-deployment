<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();
const swal = useSwal();
const permission = useUserPermission().getByName('user')
const router = useRouter();
viewWrapper.setPageTitle('Kullanıcı Oluştur');

if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'))
}

</script>
<template>
    <PageContent>
        <UserForm v-if="permission.create" />
    </PageContent>
</template>

<style scoped></style>