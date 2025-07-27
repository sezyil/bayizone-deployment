<script setup lang="ts">
import { IOfferFilter } from './COrderFilter.vue';
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import CustomerOrderApi from '/@src/request/request-customer-order';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { EnumCustomerOrderStatus, getCustomerOrderStatusText } from '/@src/shared/form/interface-customer-order';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const apiClass = new CustomerOrderApi();
const tableUri = apiClass.tableUri;
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Siparişler");
const initialized = ref(false);

const filter = ref<IOfferFilter>({});

const refreshTable = ref(false);
const handleSearch = (filterData: IOfferFilter) => {
    initialized.value = true;
    filter.value = filterData;
    refreshTable.value = true;
}
const tableData = ref([]);

interface IResponse {
    id: string
    company_customer_id: string
    company_customer_name: string
    grand_total: string
    formatted_grand_total: string
    order_date: string
    order_no: string
    currency: string
    currency_name: string
    status: EnumCustomerOrderStatus
    preview_link: string
}

const dataContent = ref<IDataTableColumn[]>([
    { data: "company_customer_name", headText: "Müşteri Adı" },
    { data: "order_no", headText: "Sipariş No", render: (row: any, data: string) => data ? data : "---" },
    { data: "order_date", headText: "Sipariş Tarihi" },
    { data: "formatted_grand_total", headText: "Toplam Tutar" },
    { data: "currency_name", headText: "Para Birimi" },
    { data: "status", headText: "Durum", render: (row: any, data: string) => getCustomerOrderStatusText(data as EnumCustomerOrderStatus) },
    {
        data: "id",
        headText: "İşlemler",
        render: (row: IResponse, data: any, index) => {
            return {
                isRaw: false,
                nodes: statusNodeGenerator(row.status, row, data)
            };
        },
    },
]);

const statusNodeGenerator = (status: EnumCustomerOrderStatus, row: IResponse, data: any): IDataTableColumnNode[] => {
    let tmpNodes: IDataTableColumnNode[] = [];
    let editNodeActiveStatuses = [EnumCustomerOrderStatus.DRAFT]
    let cancelNodeActiveStatuses = [EnumCustomerOrderStatus.DRAFT]
    let commonNodes: IDataTableColumnNode[] = [
        //for preview
        {
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "invoicePreview",
                firstParam: row.preview_link,
            },
            title: "Sipariş Çıktısı",
            innerHTML: `<i class="fas fa-file-invoice"></i>`
        },
        /* show */
        {
            elementType: "button",
            isRaw: false,
            nodeActive: true,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "showItem",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Görüntüle",
            innerHTML: `<i class="fas fa-eye"></i>`
        },
        {
            elementType: "button",
            isRaw: false,
            nodeActive: editNodeActiveStatuses.includes(row.status),
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "editItem",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Düzenle",
            innerHTML: `<i class="fas fa-pencil-alt"></i>`
        },
        {
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "deleteItem",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Sil",
            innerHTML: `<i class="fas fa-trash-alt"></i>`
        },

    ];
    return [
        ...tmpNodes,
        ...commonNodes
    ];
}

const _redir = (() => {
    const _tmpUri = "/app/customer_orders/";
    const _edit = (id: string) => router.push(_tmpUri + id + "/edit");
    const _preview = (id: string) => router.push(_tmpUri + id + "/show");

    return {
        edit: _edit,
        preview: _preview
    }
})()

const deleteItem = async (id: string) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: "Hayır"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.remove(id);
                swal.fire("Başarılı", "Silindi", "success"), refreshTable.value = true;
            } catch (err) {
                catchFieldError(err)
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}








const eventWatcher = (e: IDataTableCellTrigger) => {
    apiClass.setCompanyId(e.secondParam);
    switch (e.name) {
        case "editItem":
            _redir.edit(e.firstParam);
            break;
        case "deleteItem":
            deleteItem(e.firstParam);
            break;
        case "invoicePreview":
            window.open(e.firstParam, "_blank");
            break;
        case "showItem":
            _redir.preview(e.firstParam);
            break;
        default:
            swal.fire("Hata", "Beklenmeyen bir hata oluştu(EW_261)", "error");
            break;
    }

};

onMounted(async () => {

})
</script>
<template>
    <COrderFilter @search-triggered="handleSearch" />
    <VDatatable v-if="initialized" :card-title="cardTitle" :request-u-r-l="tableUri" :data-content="dataContent"
        :filter="filter" @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />

</template>