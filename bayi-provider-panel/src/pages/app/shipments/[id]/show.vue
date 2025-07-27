<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import ShipmentsApi from '/@src/request/request-shipments';
import { ShipmentDetailResponse } from '/@src/shared/response/interface-shipment-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const viewWrapper = useViewWrapper();
const api = new ShipmentsApi();
const router = useRouter();
const route = useRoute();
const { t } = useI18n();

const { id: shipment_id } = route.params as { id: string };
//if param id not string or empty redirect to offer page
if (!shipment_id) router.push('/app/offers');


const detailData = ref<ShipmentDetailResponse.IShipmentDetailResponse>();

const getData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.get(shipment_id);
        let _data = data.data as ShipmentDetailResponse.IShipmentDetailResponse;
        detailData.value = _data;
    } catch (error) {
        catchFieldError(error);
    } finally {
        viewWrapper.setLoading(false);
    }
}

const downloadExcel = async (language: 'tr' | 'en') => {
    if (!detailData.value) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.downloadExcel(shipment_id, language);
        const url = window.URL.createObjectURL(new Blob([data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `bayizone-${detailData.value.shipment_no}-${language}.xlsx`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (e) {
        catchFieldError(e);
    } finally {
        viewWrapper.setLoading(false);
    }
}

onMounted(async () => {
    await getData();
})

</script>
<template>
    <PageContent>
        <template #header>
            <div class="btn-group" v-if="detailData">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">İşlemler</button>
                <div class="dropdown-menu dropdown-menu-right" ref="dropdownMenu">
                    <button @click="downloadExcel('tr')" class="dropdown-item">{{ t('components.orders.downloadExcel')
                        }} (TR)</button>
                    <button @click="downloadExcel('en')" class="dropdown-item">{{ t('components.orders.downloadExcel')
                        }} (EN)</button>
                </div>
            </div>
        </template>

        <div v-if="detailData">
            <div class="row">
                <div class="col-12 col-md-8 order-2 order-md-1">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ t('sidemenu.product') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table  table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ t('common.order.no') }}</th>
                                            <th>{{ t('common.product') }}</th>
                                            <th>{{ t('common.sent') }}</th>
                                            <th>{{ t('variant.COLOR') }}</th>
                                            <th>{{ t('variant.DIMENSION') }}</th>
                                            <th>{{ t('components.shipment.unit_volume') }}</th>
                                            <th>{{ t('components.shipment.total_volume') }}</th>
                                            <th>{{ t('components.shipment.unit_package') }}</th>
                                            <th>{{ t('components.shipment.total_package') }}</th>
                                            <th>{{ t('components.shipment.unit_weight') }}</th>
                                            <th>{{ t('components.shipment.total_weight') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 13px;">
                                        <tr v-for="product in detailData.lines" :key="product.id">
                                            <td>{{ product.order_no }}</td>
                                            <td>
                                                <!-- name with code -->
                                                <span>{{ product.product_name }}</span>
                                                <span class="badge bg-info-100 rounded-pill text-black">{{
                                                    product.product_code
                                                }}</span>
                                            </td>
                                            <td>{{ product.quantity }}</td>
                                            <td>
                                                <div class="d-flex gap-1 flex-column" v-if="product.color">
                                                    <span v-for="color in product.color"
                                                        class="badge bg-secondary text-white">
                                                        {{ color.variant.name }}
                                                        : {{ color.variant_value.name }}
                                                    </span>
                                                </div>
                                                <span v-else>---</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-column" v-if="product.dimension">
                                                    <span v-for="dimension in product.dimension"
                                                        class="badge bg-secondary text-white">

                                                        {{ dimension.variant.name }}
                                                        :
                                                        <br />
                                                        {{ t('components.offer_request.elements.product.length') }}:
                                                        {{ dimension.variant_value.value.length }}cm x
                                                        {{ t('components.offer_request.elements.product.width') }}:
                                                        {{ dimension.variant_value.value.width }}cm x
                                                        {{ t('components.offer_request.elements.product.height') }}:
                                                        {{ dimension.variant_value.value.height }}cm
                                                    </span>
                                                </div>
                                                <span v-else>---</span>
                                            </td>
                                            <td>{{ product.unit_volume }}</td>
                                            <td>{{ product.total_volume }}</td>
                                            <td>{{ product.unit_package }}</td>
                                            <td>{{ product.total_package }}</td>
                                            <td>{{ product.weight }}</td>
                                            <td>{{ product.total_weight }}</td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>{{ t('common.total') }}</td>
                                            <td>{{ detailData.total_volume }}</td>
                                            <td></td>
                                            <td>{{ detailData.total_package }}</td>
                                            <td></td>
                                            <td>{{ detailData.total_weight }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- history -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ t('common.statusHistory') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ t('common.process_date') }}</th>
                                            <th>{{ t('common.action') }}</th>
                                            <th>{{ t('common.note') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="history in detailData.histories" :key="history.id">
                                            <td>{{ history.status_label }}</td>
                                            <td>{{ history.created_at }}</td>
                                            <td>{{ history.note }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- only preview -->
                <div v-if="detailData" class="col-12 col-md-4 order-1 order-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ t('actions.detail') }}</h3>
                        </div>
                        <div class="card-body">
                            <!-- customer no input -->
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ t('components.shipment.shipment_no') }}
                                    <span class="badge bg-primary rounded-pill text-white">
                                        #{{ detailData?.shipment_no }}
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
                                    {{ t('components.shipment.sent_date') }}
                                    <span v-if="detailData.shipped_at">{{ detailData.shipped_at }}</span>
                                    <span v-else>
                                        <span class="badge bg-warning rounded-pill text-white">{{
                                            t('components.shipment.not_sent') }}</span>
                                    </span>
                                </li>
                                <!-- delivered_at -->
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ t('components.shipment.deliver_date') }}
                                    <span v-if="!detailData.shipped_at">
                                        <span class="badge bg-warning rounded-pill text-white">{{
                                            t('components.shipment.not_sent') }}.</span>
                                    </span>
                                    <span v-else-if="detailData.delivered_at">{{ detailData.delivered_at }}</span>
                                    <span v-else>
                                        <span class="badge bg-warning rounded-pill text-white">{{
                                            t('components.shipment.not_delivered') }}.</span>
                                    </span>
                                </li>
                                <!-- created -->
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ t('common.filter.createDate') }}
                                    <span>{{ detailData?.created_at }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ t('common.updated_at') }}
                                    <span>{{ detailData?.updated_at }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PageContent>
</template>


<style scoped></style>