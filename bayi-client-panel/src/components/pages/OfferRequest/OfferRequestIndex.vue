<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.OFFER_REQUESTS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { remove } from '/@src/request/request-user';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const props = defineProps({
    canCreate: {
        type: Boolean,
        default: false
    }
})

interface IResponse {
    id: string
    company_id: string
    company_name: string
    global_note: string | null
    created_at: string
    item_count: string
    currency: string
    status: string
    from_store: boolean
}
const router = useRouter();
const cardTitle = ref("Teklif İstekleri");
enum OfferRequestStatus {
    PENDING = "PENDING",
    ACCEPTED = "ACCEPTED",
    REJECTED = "REJECTED"
}

const statusConverter = (status: OfferRequestStatus) => {
    switch (status) {
        case OfferRequestStatus.PENDING:
            return `<span class="badge badge-warning">Beklemede</span>`;
        case OfferRequestStatus.ACCEPTED:
            return `<span class="badge badge-success">Onaylandı</span>`;
        case OfferRequestStatus.REJECTED:
            return `<span class="badge badge-danger">Reddedildi</span>`;
        default:
            return `<span class="badge badge-warning">Beklemede</span>`;
    }
}
const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "created_at", headText: "Oluşturulma Tarihi" },
    { data: "company_name", headText: "Müşteri" },
    { data: "item_count", headText: "Ürün Sayısı" },
    { data: "currency", headText: "Para Birimi" },
    { data: "from_store", headText: "Store İstek", render: (row, data) => data ? '<span class="badge badge-success">Evet</span>' : '<span class="badge badge-danger">Hayır</span>' },
    { data: "global_note", headText: "Genel Not", render: (row, data) => data ?? '---' },
    { data: "status", headText: "Durum", render: (row, data) => statusConverter(data) },
    {
        data: "id",
        headText: "Eylemler",
        render: (row: IResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        nodeActive: props?.canCreate && row.status == OfferRequestStatus.PENDING,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                            secondParam: row.company_id
                        }, innerHTML: `<i class="fas fa-save"></i>`,
                    }
                ]
            }
        }
    }
]);



const _redir = (str: any) => {
    router.push('/app/offers/create?request_id=' + str);
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
};

</script>