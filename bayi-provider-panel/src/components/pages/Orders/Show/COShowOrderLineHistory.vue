<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
const props = defineProps<{
    lineItemData: ICustomerOrderShowResponse['lines'][0]
    orderId: string;
}>();
const { t } = useI18n();
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
                                    <th>{{ t('common.process_date') }}</th>
                                    <th>{{ t('common.status') }}</th>
                                    <th>{{ t('common.note') }}</th>
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
                            {{ t('components.orders.historyNotExist') }}.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped></style>