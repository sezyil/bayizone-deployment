<script setup lang="ts">
import Dialog from 'primevue/dialog';
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import ShipmentsApi from '/@src/request/request-shipments';
import { ShipmentDetailResponse } from '/@src/shared/response/interface-shipment-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const viewWrapper = useViewWrapper();
const swal = useSwal();
const api = new ShipmentsApi();
const router = useRouter();
const route = useRoute();
const shippedDialogVisibility = ref(false);
const deliverDialogVisibility = ref(false);
const dialogData = ref<{
    date: string;
    note: string;
    notify: boolean;
}>({
    date: '',
    note: '',
    notify: false
});

const resetDialogData = () => {
    dialogData.value = {
        date: '',
        note: '',
        notify: false
    };
}

const { id: shipment_id } = route.params as { id: string };
//if param id not string or empty redirect to offer page
if (!shipment_id) router.push('/app/offers');

const permission = useUserPermission().getByName('customer_order');
if (!permission.view) {
    await swalPermissionDenied(() => router.push('/app'));
}
viewWrapper.setPageTitle('Sevkiyat Detayı');
const detailData = ref<ShipmentDetailResponse.IShipmentDetailResponse>();
const triggerShippedDialog = () => {
    resetDialogData();
    shippedDialogVisibility.value = true;
}
const triggerDeliverDialog = () => {
    resetDialogData();
    deliverDialogVisibility.value = true;
}

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

// send shipment
const sendShipment = async (e: Event) => {
    e.preventDefault();
    viewWrapper.setLoading(true);
    try {
        await api.sendShipment(shipment_id, dialogData.value);
        shippedDialogVisibility.value = false;
        resetDialogData();
        swal.fire('Başarılı', 'Gönderi başarıyla gönderildi.', 'success');
        await getData();
    } catch (error) {
        catchFieldError(error);
    } finally {
        viewWrapper.setLoading(false);
    }
}

// deliver shipment
const deliverShipment = async (e: Event) => {
    e.preventDefault();
    viewWrapper.setLoading(true);
    try {
        await api.deliverShipment(shipment_id, dialogData.value);
        deliverDialogVisibility.value = false;
        resetDialogData();
        swal.fire('Başarılı', 'Gönderi başarıyla teslim edildi.', 'success');
        await getData();
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
        link.setAttribute('download', `bayizone-sevkiyat-${detailData.value.shipment_no}-${language}.xlsx`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (e) {
        catchFieldError(e, swal);
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
                    <button @click="downloadExcel('tr')" class="dropdown-item">Excel İndir (TR)</button>
                    <button @click="downloadExcel('en')" class="dropdown-item">Excel İndir (EN)</button>
                </div>
            </div>
        </template>

        <div v-if="detailData">

            <div class="row">
                <div v-if="detailData" class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detay</h3>
                        </div>
                        <div class="card-body">
                            <!-- customer no input -->
                            <div class="row gy-2" id="x-detail-list">
                                <!-- customer -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                        <span>Müşteri</span>
                                        <RouterLink :to="'/app/customers/' + detailData?.company_customer_id">
                                            {{ detailData?.company_customer_name }}
                                        </RouterLink>
                                    </div>
                                </div>
                                <!-- shipment number -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                        <span>Sevk No</span>
                                        <span class="badge bg-primary rounded-pill text-white">
                                            #{{ detailData?.shipment_no }}
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
                                <!-- shipment date -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                        <span>Gönderim Tarihi</span>
                                        <span v-if="detailData.shipped_at">{{ detailData.shipped_at }}</span>
                                        <span v-else>
                                            <button @click="triggerShippedDialog"
                                                class="btn btn-sm btn-secondary rounded">
                                                Gönder
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <!-- delivery date -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                        <span>Teslim Tarihi</span>
                                        <span v-if="!detailData.shipped_at">
                                            <span class="badge bg-warning rounded-pill text-white">Gönderilmedi.</span>
                                        </span>
                                        <span v-else-if="detailData.delivered_at">{{ detailData.delivered_at }}</span>
                                        <span v-else>
                                            <button @click="triggerDeliverDialog"
                                                class="btn btn-sm btn-primary rounded flash">
                                                Teslim Edildi
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <!-- created date -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                        <span>Oluşturma Tarihi</span>
                                        <span>{{ detailData?.created_at }}</span>
                                    </div>
                                </div>
                                <!-- updated date -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                        <span>Güncelleme Tarihi</span>
                                        <span>{{ detailData?.updated_at }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ürünler</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table  table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sipariş No</th>
                                            <th>Ürün Adı</th>
                                            <th>Gönderilen</th>
                                            <th>Renk</th>
                                            <th>Boyut</th>
                                            <th>Birim Fiyat</th>
                                            <th>Toplam Fiyat</th>
                                            <th>Br. Hacim</th>
                                            <th>T. Hacim</th>
                                            <th>Br. Paket</th>
                                            <th>T. Paket</th>
                                            <th>Br. Ağırlık</th>
                                            <th>T. Ağırlık</th>
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
                                                    <span v-for="color in product.color" class="badge bg-secondary text-white">
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
                                                        Y: {{ dimension.variant_value.value.height
                                                        }}x
                                                        G: {{ dimension.variant_value.value.width
                                                        }}x
                                                        U: {{ dimension.variant_value.value.length
                                                        }}
                                                    </span>
                                                </div>
                                                <span v-else>---</span>
                                            </td>
                                            <td>{{ product.unit_price }}</td>
                                            <td>{{ product.grand_total }}</td>
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
                                            <td></td>
                                            <td>Toplamlar</td>
                                            <td>{{ detailData.lines.reduce((acc, cur) => acc + cur.quantity, 0) }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ detailData.grand_total }}</td>
                                            <td></td>
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
                            <h3 class="card-title">Geçmiş</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>İşlem Tarihi</th>
                                            <th>İşlem</th>
                                            <th>Not</th>
                                            <th>Bildirim</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="history in detailData.histories" :key="history.id">
                                            <td>{{ history.status_label }}</td>
                                            <td>{{ history.created_at }}</td>
                                            <td>{{ history.note }}</td>
                                            <td>{{ history.notify ? 'Evet' : 'Hayır' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- only preview -->

            </div>
        </div>

        <Dialog v-model:visible="shippedDialogVisibility" modal header="Gönderi Bilgisi" :style="{ width: '25rem' }">
            <form @submit.prevent="sendShipment">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="date_1">Gönderim Tarihi</label>
                            <input v-model="dialogData.date" type="date" class="form-control" id="date_1" required>
                        </div>
                        <div class="form-group">
                            <textarea v-model="dialogData.note" class="form-control text-left" id="note" rows="3"
                                style="max-width: 100%" placeholder="Not"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input v-model="dialogData.notify" class="form-check-input" type="checkbox"
                                    id="spd_notify" :true-value="true" :false-value="false">
                                <label class="form-check-label" for="spd_notify">Bildirim Gönder</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-1">
                    <button @click.prevent="shippedDialogVisibility = false" class="btn btn-secondary">Kapat</button>
                    <button type="submit" class="btn btn-primary">Gönder</button>
                </div>
            </form>
        </Dialog>
        <Dialog v-model:visible="deliverDialogVisibility" modal header="Not" :style="{ width: '25rem' }">
            <form @submit.prevent="deliverShipment">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="date_2">Teslim Tarihi</label>
                            <input v-model="dialogData.date" type="date" class="form-control" id="date_2" required>
                        </div>
                        <div class="form-group">
                            <textarea v-model="dialogData.note" class="form-control text-left" id="note" rows="3"
                                style="max-width: 100%" placeholder="Not"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input v-model="dialogData.notify" class="form-check-input" type="checkbox"
                                    id="dvr_notify" :true-value="true" :false-value="false">
                                <label class="form-check-label" for="dvr_notify">Bildirim Gönder</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-1">
                    <button @click.prevent="deliverDialogVisibility = false" class="btn btn-secondary">Kapat</button>
                    <button type="submit" class="btn btn-primary">Teslim Et</button>
                </div>
            </form>

        </Dialog>

    </PageContent>
</template>


<style scoped>
#x-detail-list [class*='col-'] {
    margin-bottom: 0.5rem;
}
</style>