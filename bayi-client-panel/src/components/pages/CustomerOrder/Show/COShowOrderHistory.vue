<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import CustomerOrderApi from '/@src/request/request-customer-order';
import { EnumCustomerOrderStatus, globCustomerOrderStatusList } from '/@src/shared/form/interface-customer-order';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
export interface IOrderHistoryUpdate {
    status: EnumCustomerOrderStatus | ''
    managedBySystem?: boolean;
    note: string;
    notify: boolean;
}
const viewWrapper = useViewWrapper();
const api = new CustomerOrderApi();
const swal = useSwal();
const emit = defineEmits<{
    (e: 'update'): void;
}>();

const managedBySystem = ref<boolean>(false);

const props = defineProps<{
    historyData: ICustomerOrderShowResponse['histories'];
    orderId: string;
    orderStatus: EnumCustomerOrderStatus;
    managingSystem: boolean;
}>();
const updateData = ref<IOrderHistoryUpdate>({
    status: '',
    note: '',
    notify: false
});

// Tüm durumları içeren bir tür oluşturuyoruz
type StatusMappingType = {
    [key in EnumCustomerOrderStatus]: EnumCustomerOrderStatus[];
}

const statusMapping: StatusMappingType = {
    [EnumCustomerOrderStatus.DRAFT]: [
        EnumCustomerOrderStatus.APPROVED,
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.CANCELED
    ],
    [EnumCustomerOrderStatus.APPROVED]: [
        EnumCustomerOrderStatus.IN_PRODUCTION,
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.COMPLETED,
        EnumCustomerOrderStatus.CANCELED
    ],
    [EnumCustomerOrderStatus.IN_PRODUCTION]: [
        EnumCustomerOrderStatus.READY_TO_SHIP,
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.CANCELED
    ],
    [EnumCustomerOrderStatus.READY_TO_SHIP]: [
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.CANCELED,
        EnumCustomerOrderStatus.SHIPPED,
        EnumCustomerOrderStatus.PARTIALLY_SHIPPED,
    ],
    [EnumCustomerOrderStatus.PARTIALLY_SHIPPED]: [
        EnumCustomerOrderStatus.SHIPPED,
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.CANCELED
    ],
    [EnumCustomerOrderStatus.SHIPPED]: [
        EnumCustomerOrderStatus.DELIVERED,
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.CANCELED
    ],
    [EnumCustomerOrderStatus.DELIVERED]: [
        EnumCustomerOrderStatus.COMPLETED,
        EnumCustomerOrderStatus.REJECTED,
        EnumCustomerOrderStatus.CANCELED
    ],
    [EnumCustomerOrderStatus.REJECTED]: [],
    [EnumCustomerOrderStatus.CANCELED]: [],
    [EnumCustomerOrderStatus.COMPLETED]: [],
};

const nextStatusList = computed(() => {
    const currentStatus = props.orderStatus as EnumCustomerOrderStatus;
    return globCustomerOrderStatusList.filter(status => statusMapping[currentStatus]?.includes(status.value))
});


const handleUpdate = async () => {
    viewWrapper.setLoading(true);
    if (!updateData.value.status) {
        swal.fire('Hata', 'Durum seçimi yapmalısınız', 'error');
        viewWrapper.setLoading(false);
        return;
    }
    try {
        let data = { ...updateData.value } as any;
        if (data.status === EnumCustomerOrderStatus.READY_TO_SHIP) {
            data['managed_by_system'] = managedBySystem.value;
        }
        await api.updateStatus({
            note: data.note,
            status: data.status,
            send_notify: data.notify,
            managed_by_system: data?.managed_by_system
        }, props.orderId);
        swal.fire({
            icon: 'success',
            title: 'Durum Güncellendi',
            text: 'Sipariş durumu başarıyla güncellendi.',
        });
        updateData.value = {
            status: '',
            note: '',
            notify: false
        };
        managedBySystem.value = false;


        emit('update');
    } catch (e) {
        catchFieldError(e, swal);
    } finally {
        viewWrapper.setLoading(false);
    }
}


</script>
<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>İşlem Tarihi</th>
                                    <th>Durum</th>
                                    <th>Not</th>
                                    <th>Bilgi Maili</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="history in historyData" :key="history.id">
                                    <td>{{ history.created_at }}</td>
                                    <td>{{ history.status_label }}</td>
                                    <td>{{ history.note }}</td>
                                    <td>{{ history.notify ? 'Evet' : 'Hayır' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-12">
            <!-- sipariş durum geçmiş değiştir -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sipariş Durum Değiştir</h3>
                </div>
                <div v-if="managingSystem" class="card-body">
                    <div class="alert alert-warning">
                        <p class="m-0">Sipariş durumu değiştirilemez. Sevkiyat sistemi kullanıldığı için durum
                            değişikliği
                            yapılamaz.</p>
                    </div>
                </div>
                <div class="card-body" v-else-if="nextStatusList.length > 0">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cx-status">Durum</label>
                                <select class="form-control" id="cx-status" v-model="updateData.status">
                                    <option value="">Seçiniz</option>
                                    <option v-for="status in nextStatusList" :key="status.value"
                                        :value="status.value.toString()">
                                        {{ status.title }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- system controlled v-if updateData.status is READY_TO_SHIP  -->
                        <div class="col-12 " v-if="updateData.status == EnumCustomerOrderStatus.READY_TO_SHIP">
                            <div class="form-group border rounded p-1">
                                <!-- checkbox -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cx-notify"
                                        v-model="managedBySystem" :true-value="true" :false-value="false">
                                    <label class="form-check-label" for="cx-notify">
                                        Sevkiyat sistemi kullanılsın mı?
                                    </label>
                                </div>
                                <small class="text-warning">Eğer bu seçenek tercih edilirse, sipariş durumunu manuel
                                    olarak güncelleyemeyeceksiniz. Sistem otomatik olarak bu siparişin bulunduğu
                                    sevkiyatlarla bağlantı
                                    kuracak ve sevkiyat gönderildiği/teslim edildiği zaman sipariş durumunu
                                    güncelleyecektir.</small>
                            </div>
                        </div>
                        <!-- açıklama -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="cx-note">Açıklama</label>
                                <textarea class="form-control" id="cx-note" rows="3"
                                    v-model="updateData.note"></textarea>
                            </div>
                        </div>

                        <!-- bilgi maili -->
                        <div class="col-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="notify"
                                        v-model="updateData.notify" :true-value="true" :false-value="false">
                                    <label class="form-check-label" for="notify">
                                        Müşteriyi bilgilendir
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <VButtonSave @click="handleUpdate" />
                        </div>
                    </div>
                </div>
                <div class="card-body" v-else>
                    <div class="alert alert-warning">
                        <p>Sipariş durumu değiştirilemez. Tamamlanmış siparişlerde durum değişikliği yapılamaz.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped></style>