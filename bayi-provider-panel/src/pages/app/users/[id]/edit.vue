<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const { t } = useI18n();
const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle(t('components.users.edit.title'));

const { id: user_id } = route.params as { id: string };

//if param id not string or empty redirect to user page
if (!user_id) router.push('/app/users');

</script>

<template>
    <PageContent>
        <UserForm :user_id="user_id" />
    </PageContent>
</template>

<style lang="scss" scoped></style>