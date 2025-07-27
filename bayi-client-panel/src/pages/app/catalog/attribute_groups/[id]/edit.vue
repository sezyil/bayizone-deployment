<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle('Özellik Grubu Düzenle');
const permission = useUserPermission().getByName('attribute_group');
if (!permission?.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
const { id } = route.params as { id: string };
const attribute_group_id = Number(id);

//if param id not number redirect to attribute group page
if (isNaN(attribute_group_id)) router.push('/app/catalog/attribute_groups');

</script>
<template>
    <PageContent>
        <AttributeGroupForm :param_id="attribute_group_id" v-if="permission?.update" />
    </PageContent>
</template>



<style scoped></style>