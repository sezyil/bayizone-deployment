<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import CustomerNoteApi from '/@src/request/request-customer-note';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';

const props = defineProps({
    id: {
        type: String,
        required: false
    },
    company_customer_id: {
        type: String,
    },
    modalStatus: {
        type: Boolean,
        default: false,
    },
})
const emit = defineEmits(['close', 'update'])
const swal = useSwal()
const api = new CustomerNoteApi(props.company_customer_id)
const buttonText = computed(() => props.id ? 'Güncelle' : 'Ekle')
const dataNote = ref<string>('')
const isLoading = ref<boolean>(false)
const formErrors = ref<{ note: string[] }>({
    note: []
})
const saveNote = async () => {
    isLoading.value = true
    try {
        if (props.id) {
            await api.update({ note: dataNote.value }, props.id)
        } else {
            await api.add({ note: dataNote.value })
        }
        swal.fire('Başarılı', 'Not başarıyla kaydedildi', 'success').then(() => {
            emit('update')
        })
    } catch (error) {
        catchFieldError(error, (field: any, message: any) => {
            //@ts-ignore
            formErrors.value[field] = [message];
        });
    }
    isLoading.value = false
}

watch(() => props.modalStatus, (value) => {
    if (value) {
        dataNote.value = ''
        formErrors.value = { note: [] }
    }
})

watch(() => props.id, (value) => {
    if (value) {
        isLoading.value = true
        api.get(value).then(({ data }) => {
            dataNote.value = data.data.note
        })
        isLoading.value = false
    }
})

</script>
<template>
    <VModal :show="modalStatus" @close="emit('close')" :loader="isLoading">
        <!-- textarea 500 -->
        <template #default>
            <InputWithError :errors="formErrors.note">
                <div class="form-group">
                    <label for="note">Not</label>
                    <textarea class="form-control" id="note" rows="5" v-model="dataNote"></textarea>
                </div>
            </InputWithError>
        </template>

        <!-- footer -->
        <template #footer>
            <button type="button" class="btn btn-bayi-red" @click="saveNote">{{ buttonText }}</button>
        </template>
    </VModal>
</template>



<style scoped></style>