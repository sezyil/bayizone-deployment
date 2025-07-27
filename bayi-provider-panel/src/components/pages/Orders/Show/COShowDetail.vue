<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
const { t } = useI18n();
const props = defineProps<{
    detailData: ICustomerOrderShowResponse['detail'];
}>();

const tooltip = ref<any>(null);

onMounted(() => {
    const tooltipEl = tooltip.value;
    if (tooltipEl) {
        // @ts-ignore
        $(tooltipEl).tooltip();
    }
});

</script>

<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ t('actions.detail') }}</h3>
        </div>
        <div class="card-body">
            <!-- customer no input -->
            <ul class="list-group list-group-flush">
                <!-- customer -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ t('common.order.no') }}
                    <span class="badge bg-primary rounded-pill text-white">
                        #{{ detailData?.order_no }}
                    </span>
                </li>
                <!-- offer status -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ t('common.status') }}
                    <span class="badge bg-primary rounded-pill text-white">
                        {{ detailData?.status_label }}
                    </span>
                </li>
                <!-- offer date -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ t('common.order.date') }}
                    <span>{{ detailData?.order_date }}</span>
                </li>
                <!-- created -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ t('common.created_at') }}
                    <span>{{ detailData?.created_at }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ t('common.updated_at') }}
                    <span>{{ detailData?.updated_at }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
<style scoped></style>