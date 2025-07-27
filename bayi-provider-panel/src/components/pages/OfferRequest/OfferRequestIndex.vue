<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { OfferRequestApi } from '/@src/request/request-offer-request';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const { t } = useI18n();
const apiClass = new OfferRequestApi();
const swal = inject('$swal') as SwalInstance;
const cardTitle = ref(t('components.offer_requests.title'));
const isLoading = ref(false);



const refreshTable = ref(false);
const tableData = ref([]);

interface IResponse {
    id: string
    customer_id: string
    company_customer_id: string
    global_note: string
    status: OfferRequestStatus
    item_count: string
    created_at: string
    updated_at: string
    cancel_note: string
    currency: string
    is_new: boolean
}

enum OfferRequestStatus {
    PENDING = "PENDING",
    ACCEPTED = "ACCEPTED",
    REJECTED = "REJECTED"
}

const statusConverter = (status: OfferRequestStatus) => {
    let badge = "";
    let text = "";
    switch (status) {
        case OfferRequestStatus.PENDING:
            badge = 'badge-warning';
            text = t('components.offer_requests.status.pending');
            break;
        case OfferRequestStatus.ACCEPTED:
            badge = 'badge-success';
            text = t('components.offer_requests.status.accepted');
            break;
        case OfferRequestStatus.REJECTED:
            badge = 'badge-danger';
            text = t('components.offer_requests.status.rejected');
            break;
        default:
            badge = 'badge-warning';
            text = t('components.offer_requests.status.pending');
            break;
    }
    return `<span class="badge ${badge}">${text}</span>`;
}

const dataContent = ref<IDataTableColumn[]>([
    /* create date format*/
    {
        data: "created_at", headText: t('common.filter.createDate'), render: (row: any, data: string) => {
            //if is new text color is text-primary
            let badge = row.is_new ? '<span class="badge badge-primary">' + t('common.new') + '</span>' : '';
            return `<span class="badge badge-light">${data}</span> ${badge}`;
        }
    },
    { data: "item_count", headText: t('components.offer_requests.productQuantity'), render: (row: any, data: string) => data ?? "---" },
    { data: "currency", headText: t('common.currency'), render: (row: any, data: string) => data ?? "---" },
    { data: "global_note", headText: t('common.generalNote'), render: (row: any, data: string) => data ?? "---" },
    { data: "status", headText: t('common.status'), render: (row: any, data: OfferRequestStatus) => statusConverter(data) },
    {
        data: "id",
        headText: t('common.actions'),
        render: (row: IResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-primary mr-1",
                        trigger: {
                            type: "click",
                            emitName: "showItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-eye"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-danger",
                        nodeActive: row.status == OfferRequestStatus.PENDING,
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`
                    }
                ]
            }
        }
    }
]);

const deleteItem = async (id: string) => {
    swal.fire({
        title: t('actions.deleteConfirm'),
        text: t('actions.deleteNotRecoverable'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('actions.yes'),
        cancelButtonText: t('actions.no')
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.remove(id);
                swal.fire("Başarılı", t('actions.deleteSuccess'), "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire("Hata", t('actions.deleteError'), "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}
const modalId = ref("");
const showItemDetail = async (id: any) => {
    modalId.value = id;
    isLoading.value = true;
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "showItem") showItemDetail(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};


</script>
<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.OFFER_REQUESTS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
    <OfferRequestModal :id="modalId" @close="modalId = ''" />
</template>