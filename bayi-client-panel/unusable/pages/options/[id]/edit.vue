<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();
viewWrapper.setPageTitle('Seçenek Düzenle');
const permission = useUserPermission().getByName('option');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
const { id } = route.params as { id: string };
const option_id = Number(id);
//if param id not number redirect to options page
if (isNaN(option_id)) router.push('/app/catalog/options');

</script>
<template>
    <PageContent>
        <OptionsForm :param_id="option_id" v-if="permission.update" />
    </PageContent>
</template>



<style scoped></style>