<script  setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const {t} = useI18n();
const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle(t('components.warehouses.edit.title'));
const { id } = route.params as { id: string };

//if param id not string or empty redirect to user page
if (!id) router.push('/app/warehouses');
</script>

<template>
    <PageContent>
        <WarehouseForm :warehouse_id="id" />
    </PageContent>
</template>

<style lang="scss" scoped></style>