<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const route = useRoute();
const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Birim Düzenle');
const permission = useUserPermission().getByName('unit');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}

const { id } = route.params as { id: string };
const param_id = Number(id);
//if param id not number redirect to categories page
if (isNaN(param_id)) router.push('/app/catalog/product_units');


</script>
<template>
    <PageContent>
        <ProductUnitForm :param_id="param_id" v-if="permission.update" />
    </PageContent>
</template>



<style scoped></style>