<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IOfferFilter } from '../OfferRequest/OfferRequestFilter.vue';
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import API_URIS from '/@src/utils/api/api_uris';

const { t, locale } = useI18n();
const router = useRouter();
const cardTitle = ref(t('components.orders.title'));
const filter = ref<IOfferFilter>({});

interface IResponse {
    id: string
    order_no: string
    company_customer_id: string
    order_date: string
    total_price: string
    currency: string
    currency_name: string
    status: string
    uri: string
    status_text: string
}

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "order_no", headText: t('common.order.no') },
    { data: "order_date", headText: t('components.orders.table.orderDate') },
    { data: "total_price", headText: t('components.orders.table.totalPrice') },
    { data: "currency_name", headText: t('common.currency') },
    { data: "status_text", headText: t('common.status') },
    {
        data: "id",
        headText: t('common.actions'),
        render: (row: IResponse, data: any, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-primary mr-1",
                        trigger: {
                            type: "click",
                            emitName: "orderDetail",
                            firstParam: row.id
                        },
                        title: t('actions.detail'),
                        innerHTML: `<i class="fas fa-info"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-warning mr-1",
                        trigger: {
                            type: "click",
                            emitName: "invoiceAction",
                            firstParam: row.uri
                        },
                        title: t('actions.view'),
                        innerHTML: `<i class="fas fa-eye"></i>`
                    },
                ]
            };
        },
    },
]);



const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "orderDetail") {
        router.push(`/app/orders/${e.firstParam}/show`);
    } else if (e.name == "invoiceAction") {
        //open in new tab
        window.open(`${e.firstParam}&lang=${locale.value}`, '_blank');
    }
};

const emitSearch = (_filter: any) => {
    filter.value = _filter;
    refreshTable.value = true;
}


</script>
<template>
    <OrderFilter @search-triggered="emitSearch" />
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.ORDERS" :data-content="dataContent" :filter="filter"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>