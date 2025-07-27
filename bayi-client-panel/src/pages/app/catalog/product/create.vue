<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle("Ürün Oluştur");
const router = useRouter();
const permission = useUserPermission().getByName('product');
const saveTriggered = ref(false);
if (!permission?.create) {
    swalPermissionDenied(() => router.push('/app'));
}
const userSession = useUserSession();
const hasWizard = computed(() => userSession.user?.wizard_completed);
const handleSave = () => {
    if (saveTriggered.value) return;
    saveTriggered.value = true;
};

</script>
<template>
    <PageContent>
        <template #header v-if="permission?.create">
            <VButtonSave @click="handleSave" :disabled="saveTriggered" />
        </template>
        <ProductForm v-if="permission?.create" :save-triggered="saveTriggered"
            @save-triggered="saveTriggered = $event" />
    </PageContent>
</template>


<style scoped></style>