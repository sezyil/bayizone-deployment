<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IOfferFilter } from '../OfferRequest/OfferRequestFilter.vue';
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import { OfferRequestApi } from '/@src/request/request-offer-request';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { GLOB_URIS, get_product_image_url } from '/@src/utils/global_uris';
const swal = inject('$swal') as SwalInstance;
const { t } = useI18n();
const cardTitle = ref(t('components.offers.title'));
const filter = ref<IOfferFilter>({});

interface IResponse {
    id: string
    company_customer_id: string
    company_customer_name: string
    grand_total: number
    offer_date: string
    offer_due_date: string
    offer_no: string
    currency: string
    currency_name: string
    status: EnumOfferStatus
    mail_notification_date: string | null
    preview_url: string
    password: string
}

const refreshTable = ref(false);
const tableData = ref([]);



enum EnumOfferStatus {
    DRAFT = "DRAFT",
    PENDING = "PENDING",
    APPROVED = "APPROVED",
    REJECTED = "REJECTED",
    EXPIRED = "EXPIRED",
    REVISED = "REVISED",
    CANCELED = "CANCELED",
    CLOSED = "CLOSED",
}


const getOfferStatusText = (status: EnumOfferStatus): string => {
    switch (status) {
        case EnumOfferStatus.DRAFT:
            return t('components.offers.status.draft');
        case EnumOfferStatus.PENDING:
            return t('components.offers.status.pending');
        case EnumOfferStatus.APPROVED:
            return t('components.offers.status.approved');
        case EnumOfferStatus.REJECTED:
            return t('components.offers.status.rejected');
        case EnumOfferStatus.EXPIRED:
            return t('components.offers.status.expired');
        case EnumOfferStatus.REVISED:
            return t('components.offers.status.revised');
        case EnumOfferStatus.CANCELED:
            return t('components.offers.status.canceled');
        case EnumOfferStatus.CLOSED:
            return t('components.offers.status.closed');
        default:
            return t('components.offers.status.unknown');
    }
}

const dataContent = ref<IDataTableColumn[]>([
    { data: "offer_no", headText: t('components.offers.offer_no'), render: (row: any, data: string) => data ? data : "---" },
    { data: "offer_date", headText: t('components.offers.date') },
    { data: "offer_due_date", headText: t('components.offers.due_date'), render: (row: any, data: string) => data ? data : "Belirsiz" },
    { data: "grand_total", headText: t('common.total') },
    { data: "currency_name", headText: t('common.currency') },
    { data: "status", headText: t('common.status'), render: (row: any, data: string) => getOfferStatusText(data as EnumOfferStatus) },
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
                        class: "btn btn-sm btn-warning mr-1",
                        trigger: {
                            type: "click",
                            emitName: "previewItem",
                            firstParam: row.preview_url,
                            secondParam: row.password
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
    if (e.name == "previewItem") {
        window.open(e.firstParam + '?pass=' + e.secondParam, '_blank');
    }
};

const emitSearch = (_filter: any) => {
    filter.value = _filter;
    refreshTable.value = true;
}


</script>
<template>
    <OfferRequestFilter @search-triggered="emitSearch" />
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.OFFERS" :data-content="dataContent" :filter="filter"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>