<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const route = useRoute();
const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Kategori Düzenle');
const permission = useUserPermission().getByName('category');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
const { id } = route.params as { id: string };
const category_id = Number(id);
//if param id not number redirect to categories page
if (isNaN(category_id)) router.push('/app/catalog/categories');


</script>
<template>
    <PageContent>
        <CategoriesForm :param_id="category_id" v-if="permission.update" />
    </PageContent>
</template>



<style scoped></style>