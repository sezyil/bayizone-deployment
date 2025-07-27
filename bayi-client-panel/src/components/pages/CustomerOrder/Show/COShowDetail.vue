<script setup lang="ts">
import { EnumCustomerOrderStatus } from '/@src/shared/form/interface-customer-order';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';

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
            <h3 class="card-title">Detay</h3>
        </div>
        <div class="card-body">
            <div class="row gy-2" id="customer-order-detail">
                <!-- customer -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span>Müşteri</span>
                        <RouterLink :to="'/app/customers/' + detailData?.company_customer_id">
                            {{ detailData?.company_customer_name }}
                        </RouterLink>
                    </div>
                </div>
                <!-- order number -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span>Sipariş No</span>
                        <span class="badge bg-primary rounded-pill text-white">
                            #{{ detailData?.order_no }}
                        </span>
                    </div>
                </div>
                <!-- offer status -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span>Durum</span>
                        <span class="badge bg-primary rounded-pill text-white">
                            {{ detailData?.status_label }}
                        </span>
                    </div>
                </div>
                <!-- offer date -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span>Sipariş Tarihi</span>
                        <span>{{ detailData?.order_date }}</span>
                    </div>
                </div>
                <!-- managed_by_system -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <p class="mb-0">
                            Sistem Yönetiminde
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" ref="tooltip"
                                data-bs-placement="top"
                                title="Sistem tarafından yönetilen siparişlerde, sipariş durumu ve notları sistem tarafından otomatik olarak güncellenir."></i>
                        </p>
                        <span>{{ detailData?.managed_by_system ? 'Evet' : 'Hayır' }}</span>
                    </div>
                </div>
                <!-- created -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span>Oluşturma Tarihi</span>
                        <span>{{ detailData?.created_at }}</span>
                    </div>
                </div>
                <!-- updated -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span>Güncelleme Tarihi</span>
                        <span>{{ detailData?.updated_at }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<style scoped>
#customer-order-detail [class*='col-'] {
    margin-bottom: 0.5rem;
}
</style>