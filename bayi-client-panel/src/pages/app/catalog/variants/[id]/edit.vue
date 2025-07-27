<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const route = useRoute();
const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Varyant Düzenle');
const permission = useUserPermission().getByName('variant');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}

const { id } = route.params as { id: string };

if (!id) router.push('/app/catalog/variants');


</script>
<template>
    <PageContent>
        <VariantForm :param_id="id" v-if="permission.update" />
    </PageContent>
</template>



<style scoped></style>