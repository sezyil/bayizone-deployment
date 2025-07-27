<script setup lang="ts">
import TabView from 'primevue/tabview';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import TabPanel from 'primevue/tabpanel';
import OrderApi from '/@src/request/request-order';
import { EnumCustomerOrderStatus } from '/@src/shared/order/interface-order';
import { useI18n } from 'vue-i18n';
const viewWrapper = useViewWrapper();
const api = new OrderApi();
const router = useRouter();
const route = useRoute();
const { t } = useI18n();
const { id: order_id } = route.params as { id: string };
//if param id not string or empty redirect to offer page
if (!order_id) router.push('/app/offers');


const orderData = ref<ICustomerOrderShowResponse | null>(null);
const detailData = computed(() => orderData.value?.detail);
const showingLineItemId = ref<ICustomerOrderShowResponse['lines'][0]['id'] | null>(null);
const showingLineItem = computed(() => orderData.value?.lines.find(line => line.id === showingLineItemId.value) || null);
const activeTabIndex = ref(0);

const getData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.get(order_id);
        orderData.value = data.data as ICustomerOrderShowResponse;
        viewWrapper.setPageTitle(t('components.orders.showTitle', [orderData.value.detail.order_no]));
    } catch (e) {
        catchFieldError(e);
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
        link.setAttribute('download', `bayizone-${order_id}-${language}.xlsx`);
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
});

</script>
<template>
    <PageContent>
        <template #header>
            <div class="btn-group" v-if="orderData">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">{{
                    t('common.actions') }}</button>
                <div class="dropdown-menu dropdown-menu-right" ref="dropdownMenu">
                    <button @click="downloadExcel('tr')" class="dropdown-item">{{ t('components.orders.downloadExcel')
                        }} (TR)</button>
                    <button @click="downloadExcel('en')" class="dropdown-item">{{ t('components.orders.downloadExcel')
                        }} (EN)</button>
                </div>
            </div>
        </template>

        <div v-if="orderData">
            <div class="row">
                <div class="col-12 col-md-8 order-2 order-md-1">
                    <COShowProducts :lineData="orderData.lines" :grandTotal="orderData.detail.grand_total"
                        :active-line-id="showingLineItemId" @look-line-history="handleLookLineHistory" />
                    <TabView id="order-detail-tabview" :activeIndex="activeTabIndex"
                        @tab-change="handleTabChange($event.index)">
                        <TabPanel :header="t('components.orders.statusHistory')" header-class="text-black">
                            <COShowOrderHistory :history-data="orderData.histories" :order-id="order_id"
                                :managing-system="orderData.detail.managed_by_system"
                                :order-status="orderData.detail.status as EnumCustomerOrderStatus" />
                        </TabPanel>
                        <TabPanel :header="t('components.orders.lineHistory')" header-class="text-black">
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
                                                                {{ t('common.productCode') }}:
                                                                <span
                                                                    class="badge bg-primary rounded-pill text-white">{{
                                                                        showingLineItem.product_code
                                                                    }}</span>
                                                            </li>
                                                            <!-- item name -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                {{ t('common.product') }}:
                                                                <span>{{ showingLineItem.product_name }}</span>
                                                            </li>
                                                            <!-- item quantity -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                {{ t('common.quantity') }}:
                                                                <span>{{ showingLineItem.quantity }}</span>
                                                            </li>
                                                            <!-- gönderilen  -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                {{ t('components.orders.sentquantity') }}:
                                                                <span>{{ showingLineItem.sent_quantity }}</span>
                                                            </li>
                                                            <!-- kalan adet -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                {{ t('components.orders.remainQuantity') }}:
                                                                <span>{{ showingLineItem.remaining_quantity }}</span>
                                                            </li>
                                                            <!-- item status -->
                                                            <li class="list-inline-item border p-1 rounded">
                                                                {{ t('common.status') }}:
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
                                                    :order-id="order_id" />
                                            </div>
                                        </div>
                                        <div v-else>
                                            <p>{{ t('components.orders.orderline.notselectedfount') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                    </TabView>
                </div>
                <!-- only preview -->
                <div v-if="detailData" class="col-12 col-md-4 order-1 order-md-2">
                    <COShowDetail :detailData="detailData" />
                </div>
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