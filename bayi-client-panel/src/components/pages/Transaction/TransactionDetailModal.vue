<script setup lang="ts">
import TransactionApi from '/@src/request/request-transaction';
import { ITransactionShow } from '/@src/shared/transaction/transaction-show';
import { EnumTransactionIO, TransactionIOTypeDescription } from '/@src/utils/form/transaction_form';

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    show: {
        type: Boolean,
        required: true
    }
})
const api = new TransactionApi()
const modalData = ref<ITransactionShow['data']>()
//close
const emit = defineEmits<{
    (event: 'close'): void
}>()
const loading = ref(false)
const getData = async () => {
    loading.value = true
    try {
        const { data } = await api.show(props.id)
        modalData.value = data.data
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
}

const cardTitle = computed(() => {
    if (modalData.value) {
        return modalData.value.detail.fiche_no + ' - ' + modalData.value.customer.name
    } else {
        return 'Fiş Detayı'
    }
})

//watch id
watch(() => props.id, async (newVal, oldVal) => {
    if (newVal) await getData()
})

</script>
<template>
    <VModal :show="show" :loader="loading" :title="cardTitle" @close="emit('close')">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Fiş No: {{ modalData?.detail.fiche_no }}</h6>
            <p class="card-text"><strong>Fatura Tipi:</strong> {{ modalData?.detail.fiche_type_label }}</p>
            <p class="card-text"><strong>Tarih:</strong> {{ modalData?.detail.date }}</p>
            <p class="card-text"><strong>Tutar:</strong> {{ modalData?.detail.formatted_amount }}</p>
            <p class="card-text"><strong>Ödendi mi?:</strong>
                {{ modalData?.detail.is_paid ? 'Evet' : 'Hayır' }}
            </p>
            <p class="card-text"><strong>Müşteri:</strong> {{ modalData?.customer.name }}</p>
            <p class="card-text"><strong>Giriş/Çıkış Tipi:</strong> {{
                TransactionIOTypeDescription(Number(modalData?.detail.io_type)) }}</p>
            <p class="card-text"><strong>Açıklama:</strong> {{ modalData?.detail.description ?? 'Yok' }}</p>
            <p class="card-text"><strong>Son Ödeme Tarihi:</strong> {{ modalData?.detail.due_date ?? 'Yok' }}</p>
            <p class="card-text"><strong>Oluşturulma Tarihi:</strong> {{ modalData?.detail.created_at }}</p>
            <p class="card-text"><strong>Güncellenme Tarihi:</strong> {{ modalData?.detail.updated_at }}</p>
        </div>
    </VModal>
</template>

<style scoped></style>