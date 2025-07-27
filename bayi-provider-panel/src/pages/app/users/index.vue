<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const viewWrapper = useViewWrapper();
const { t } = useI18n();
viewWrapper.setPageTitle(t('components.users.index.title'));
</script>
<template>
    <PageContent>
        <template #header>
            <RouterLink to="/app/users/create" class="btn btn-indigo">
                <i class="fas fa-plus mr-2"></i> {{ t('common.new') }}
            </RouterLink>
        </template>
        <UserIndex />
    </PageContent>
</template>



<style scoped></style>