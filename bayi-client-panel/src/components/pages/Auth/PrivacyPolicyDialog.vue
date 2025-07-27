<script setup lang="ts">
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import { requestPrivacyPolicy } from '/@src/request/request-utilities';
const props = defineProps<{
    visible: boolean;
}>();
const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'update:accept', value: boolean): void;
}>();

const handleVisibleChange = (e: boolean) => {
    if (!e) {
        emit('close');
    }
}
const pageContent = ref<HTMLDivElement | null>(null);
const contentInner = ref<string>('');

const handleGetContent = async () => {
    try {
        const { data } = await requestPrivacyPolicy();
        contentInner.value = data.data;
    } catch (error) {
        console.error(error);
    }
}

const handleHide = () => {
    emit('close');
}
const handleAccept = (value: boolean) => {
    emit('update:accept', value);
}

onMounted(async () => {
    await handleGetContent();
})

</script>
<template>
    <div>
        <div style="display: none;"></div>
        <Dialog :visible="visible" modal header="Gizlilik Sözleşmesi" :style="{ width: '50rem' }" @hide="handleHide"
            @update:visible="handleVisibleChange" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <div v-html="contentInner" ref="pageContent">
            </div>

            <template #footer>
                <Button label="Reddet" severity="secondary" @click="handleAccept(false)" />
                <Button label="Kabul Et" severity="success" @click="handleAccept(true)" />
            </template>
        </Dialog>
    </div>

</template>

<style scoped></style>