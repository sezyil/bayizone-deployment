<script  setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle('Kullanıcı Düzenle');

const { id: user_id } = route.params as { id: string };

//if param id not string or empty redirect to user page
if (!user_id) router.push('/app/users');

const permission = useUserPermission().getByName('user')
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'))
}

</script>

<template>
    <PageContent>
        <UserForm :user_id="user_id" v-if="permission.update" />
    </PageContent>
</template>

<style lang="scss" scoped></style>