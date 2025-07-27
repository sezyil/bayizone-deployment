<script  setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle('Yetki Düzenle');

const { id: role_id } = route.params as { id: string };

//if param id not string or empty redirect to user page
if (!role_id) router.push('/app/permissions');

const permission = useUserPermission().getByName('permission')
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'))
}

</script>

<template>
    <PageContent>
        <PermissionForm :role_id="role_id" v-if="permission.update" />
    </PageContent>
</template>

<style lang="scss" scoped></style>