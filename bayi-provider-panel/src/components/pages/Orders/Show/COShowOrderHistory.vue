<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { EnumCustomerOrderStatus } from '/@src/shared/order/interface-order';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
export interface IOrderHistoryUpdate {
    status: EnumCustomerOrderStatus | ''
    managedBySystem?: boolean;
    note: string;
    notify: boolean;
}
const emit = defineEmits<{
    (e: 'update'): void;
}>();


const props = defineProps<{
    historyData: ICustomerOrderShowResponse['histories'];
    orderId: string;
    orderStatus: EnumCustomerOrderStatus;
    managingSystem: boolean;
}>();
const { t } = useI18n();

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
                                    <th>{{ t('common.process_date') }}</th>
                                    <th>{{ t('common.status') }}</th>
                                    <th>{{ t('common.note') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="history in historyData" :key="history.id">
                                    <td>{{ history.created_at }}</td>
                                    <td>{{ history.status_label }}</td>
                                    <td>{{ history.note }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>


<style scoped></style>