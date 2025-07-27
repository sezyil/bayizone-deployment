<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Özellik Düzenle');
const router = useRouter();
const route = useRoute();
const permission = useUserPermission().getByName('attribute');
if (!permission?.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
const { id } = route.params as { id: string };
const attribute_id = Number(id);

//if param id not number redirect to attributes page
if (isNaN(attribute_id)) router.push('/app/catalog/attributes');

</script>
<template>
    <PageContent>
        <AttributeForm :param_id="attribute_id" v-if="permission?.update" />
    </PageContent>
</template>


<style scoped></style>