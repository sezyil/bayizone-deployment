<script setup lang="ts">
import TabView from 'primevue/tabview';
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import CustomerOrderApi from '/@src/request/request-customer-order';
import { EnumCustomerOrderStatus, getCustomerOrderStatusText } from '/@src/shared/form/interface-customer-order';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import TabPanel from 'primevue/tabpanel';
const viewWrapper = useViewWrapper();
const swal = useSwal();
const api = new CustomerOrderApi();
const router = useRouter();
const route = useRoute();

const { id: order_id } = route.params as { id: string };
//if param id not string or empty redirect to offer page
if (!order_id) router.push('/app/offers');

const permission = useUserPermission().getByName('customer_offer');
if (!permission.view) {
    await swalPermissionDenied(() => router.push('/app'));
}
viewWrapper.setPageTitle('Sipariş Detayı');
const orderData = ref<ICustomerOrderShowResponse | null>(null);
const detailData = computed(() => orderData.value?.detail);
const showingLineItemId = ref<ICustomerOrderShowResponse['lines'][0]['id'] | null>(null);
const showingLineItem = computed(() => orderData.value?.lines.find(line => line.id === showingLineItemId.value) || null);
const activeTabIndex = ref(0);

const getData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.preview(order_id);
        orderData.value = data.data as ICustomerOrderShowResponse;
        viewWrapper.setPageTitle(`Sipariş: #${orderData.value.detail.order_no} Detayı`);
    } catch (e) {
        catchFieldError(e, swal);
    } finally {
        viewWrapper.setLoading(false);
    }
}

const handleLookLineHistory = (line_id: number) => {
    showingLineItemId.value = line_id;
    activeTabIndex.value = 1;
    //scroll to 
    const element = document.getElementById('order-detail-tabview');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}
const handleTabChange = (index: number) => {
    activeTabIndex.value = index;
}

const downloadExcel = async (language: 'tr' | 'en') => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.downloadExcel(order_id, language);
        const url = window.URL.createObjectURL(new Blob([data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `bayizone-siparis-${order_id}-${language}.xlsx`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (e) {
        catchFieldError(e, swal);
    } finally {
        viewWrapper.setLoading(false);
    }
}
const editActive = computed(() => {
    const nonEditableStatuses = [EnumCustomerOrderStatus.CANCELED, EnumCustomerOrderStatus.COMPLETED, EnumCustomerOrderStatus.REJECTED];
    return !nonEditableStatuses.includes(orderData.value?.detail.status as EnumCustomerOrderStatus) && permission.update;
});


onMounted(async () => {
    await getData();
});

</script>
<template>
    <PageContent>
        <template #header>
            <div class="btn-group" v-if="orderData">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">İşlemler</button>
                <div class="dropdown-menu dropdown-menu-right" ref="dropdownMenu">
                    <RouterLink :to="'/app/customer_orders/' + order_id + '/edit'" class="dropdown-item"
                        v-if="editActive">
                        Düzenle
                    </RouterLink>
                    <button @click="downloadExcel('tr')" class="dropdown-item">Excel İndir (TR)</button>
                    <button @click="downloadExcel('en')" class="dropdown-item">Excel İndir (EN)</button>
                </div>
            </div>
        </template>

        <div v-if="orderData">
            <div v-if="detailData" class="col-12">
                <COShowDetail :detailData="detailData" />
            </div>
            <div class="row">
                <div class="col-12">
                    <COShowProducts :lineData="orderData.lines" :grandTotal="orderData.detail.grand_total"
                        :active-line-id="showingLineItemId" @look-line-history="handleLookLineHistory" />
                    <TabView id="order-detail-tabview" :activeIndex="activeTabIndex"
                        @tab-change="handleTabChange($event.index)">
                        <TabPanel header="Sipariş Durum Geçmişi" header-class="text-black">
                            <COShowOrderHistory :history-data="orderData.histories" :order-id="order_id"
                                :managing-system="orderData.detail.managed_by_system"
                                :order-status="orderData.detail.status as EnumCustomerOrderStatus" @update="getData" />
                        </TabPanel>
                        <TabPanel header="Sipariş Satır Geçmişi" header-class="text-black">
                            <div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row" v-if="showingLineItem">
                                            <div class="col-md-12">
                                                <!-- summary item short no input -->
                                                <div class="card">
                                                    <div class="card-body">
                                                        <ul class="list-inline mb-0">
                                                            <!-- item no -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                Ürün Kodu:
                                                                <span
                                                                    class="badge bg-primary rounded-pill text-white">{{
                                                                        showingLineItem.product_code
                                                                    }}</span>
                                                            </li>
                                                            <!-- item name -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                Ürün Adı:
                                                                <span>{{ showingLineItem.product_name }}</span>
                                                            </li>
                                                            <!-- item quantity -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                Adet:
                                                                <span>{{ showingLineItem.quantity }}</span>
                                                            </li>
                                                            <!-- gönderilen  -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                Gönderilen Adet:
                                                                <span>{{ showingLineItem.sent_quantity }}</span>
                                                            </li>
                                                            <!-- kalan adet -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                Kalan Adet:
                                                                <span>{{ showingLineItem.remaining_quantity }}</span>
                                                            </li>
                                                            <!-- item status -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                Durum:
                                                                <span class="badge bg-primary rounded-pill text-white">
                                                                    {{ showingLineItem.status_label }}
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <COShowOrderLineHistory :line-item-data="showingLineItem"
                                                    :order-id="order_id" @updated-line-history="getData" />
                                            </div>
                                        </div>
                                        <div v-else>
                                            <p>Sipariş Kalemi Seçilmedi/ Bulunamadı</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                    </TabView>
                </div>
                <!-- only preview -->

            </div>
        </div>



    </PageContent>
</template>

<style>
#order-detail-tabview .p-tabview-nav .p-tabview-nav-link {
    color: black !important;
}

#order-detail-tabview .p-tabview-nav-content .p-tabview-nav {
    margin-bottom: 0px !important;
}
</style>