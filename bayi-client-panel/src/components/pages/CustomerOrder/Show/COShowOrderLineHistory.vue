<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import CustomerOrderApi from '/@src/request/request-customer-order';
import { CustomerOrderLineStatus, ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const emit = defineEmits<{
    (e: 'updatedLineHistory'): void;
}>();
const props = defineProps<{
    lineItemData: ICustomerOrderShowResponse['lines'][0]
    orderId: string;
}>();
const viewWrapper = useViewWrapper();
const swal = useSwal();
const api = new CustomerOrderApi();
export interface IOrderLineHistoryNewForm {
    order_line_id: number;
    note: string;
    status: string;
    notify: boolean;
}
const allStatuses = CustomerOrderLineStatus.all().map(status => ({
    id: status,
    label: CustomerOrderLineStatus.description(status),
}));

const newLineData = ref<IOrderLineHistoryNewForm>({
    order_line_id: 0,
    note: '',
    status: '',
    notify: false,
});



const resetLineData = () => {
    newLineData.value = {
        order_line_id: 0,
        note: '',
        status: '',
        notify: false,
    };
}

const handleSave = async () => {
    let formData = newLineData.value;
    if (!formData) return swal.fire("Hata", "Satır bulunamadı", "error");
    if (!formData.order_line_id) return swal.fire("Hata", "Satır bilgisi boş olamaz", "error");
    viewWrapper.setLoading(true);
    try {
        await api.updateLineHistory(props.orderId, formData);
        swal.fire("Başarılı", "Satır geçmişi güncellendi", "success");
        resetLineData();
        emit('updatedLineHistory');
    } catch (e) {
        catchFieldError(e, swal);
    } finally {
        viewWrapper.setLoading(false);
    }
}

watch(() => props.lineItemData, (newVal) => {
    if (newVal) {
        newLineData.value.order_line_id = newVal.id;
    }
}, { immediate: true });

</script>
<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" v-if="lineItemData.history.length">
                            <thead>
                                <tr>
                                    <th>İşlem Tarihi</th>
                                    <th>Durum</th>
                                    <th>Not</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="history in lineItemData.history" :key="history.id">
                                    <th>{{ history.created_at }}</th>
                                    <td>
                                        <span class="badge bg-primary rounded-pill text-white">
                                            {{ history.status_label }}
                                        </span>
                                    </td>
                                    <td>{{ history.note ?? '---' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="d-flex justify-content-center align-items-center">
                            Sipariş geçmişi bulunamadı. Aşağıdan yeni bir işlem ekleyebilirsiniz.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped></style>